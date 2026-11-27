<?php

namespace App\Services;

use App\Models\DokumenProyek;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DokumenVersionService
{
    private const MAX_BATCH_BYTES = 104857600;

    private const MIME_TYPES = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'csv' => ['text/csv', 'text/plain', 'application/csv'],
        'txt' => ['text/plain'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/vnd.rar', 'application/x-rar-compressed', 'application/octet-stream'],
        'dwg' => ['application/acad', 'application/octet-stream', 'application/x-dwg', 'image/vnd.dwg'],
        'dxf' => ['application/dxf', 'application/octet-stream', 'image/vnd.dxf', 'text/plain'],
        'ifc' => ['application/ifc', 'application/octet-stream', 'text/plain'],
        'skp' => ['application/octet-stream', 'application/vnd.sketchup.skp'],
    ];

    public function __construct(private readonly DokumenFileService $fileService) {}

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function upload(DokumenProyek $document, array $files, User $user, ?string $note = null): int
    {
        $totalBytes = array_sum(array_map(fn (UploadedFile $file): int => (int) $file->getSize(), $files));

        if ($totalBytes > self::MAX_BATCH_BYTES) {
            throw ValidationException::withMessages(['files' => 'Ukuran total file tidak boleh melebihi 100 MB.']);
        }

        $storedPaths = [];

        try {
            DB::transaction(function () use ($document, $files, $user, $note, &$storedPaths): void {
                $lockedDocument = DokumenProyek::query()->whereKey($document->getKey())->lockForUpdate()->firstOrFail();
                $nextVersion = ((int) $lockedDocument->versiDokumen()->max('nomor_versi')) + 1;

                foreach ($files as $file) {
                    $metadata = $this->metadata($file);
                    $storageName = Str::uuid().'.'.$metadata['extension'];
                    $path = $file->storeAs('dokumen-proyek/'.$lockedDocument->getKey(), $storageName, 'local');

                    if ($path === false) {
                        throw new \RuntimeException('File dokumen gagal disimpan.');
                    }

                    $storedPaths[] = $path;
                    $lockedDocument->versiDokumen()->create([
                        'nomor_versi' => $nextVersion++,
                        'nama_file_asli' => Str::substr(basename($file->getClientOriginalName()), 0, 255),
                        'nama_file_storage' => $storageName,
                        'path_file' => $path,
                        'disk' => 'local',
                        'mime_type' => $metadata['mime_type'],
                        'ekstensi' => $metadata['extension'],
                        'ukuran_file' => $metadata['size'],
                        'checksum_sha256' => $metadata['checksum'],
                        'catatan' => $note,
                        'user_id' => $user->getKey(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            $this->fileService->cleanup('local', $storedPaths);
            throw $exception;
        }

        return count($files);
    }

    /**
     * @return array{extension: string, mime_type: string, size: int, checksum: string}
     */
    private function metadata(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        if (! isset(self::MIME_TYPES[$extension]) || ! in_array($mimeType, self::MIME_TYPES[$extension], true)) {
            throw ValidationException::withMessages(['files' => 'Format atau MIME file tidak sesuai.']);
        }

        $prefix = file_get_contents($file->getRealPath(), false, null, 0, 16) ?: '';
        if (str_starts_with($prefix, 'MZ') || str_starts_with($prefix, "\x7FELF") || str_starts_with($prefix, '#!')) {
            throw ValidationException::withMessages(['files' => 'File executable tidak diizinkan.']);
        }

        return [
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size' => (int) $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
        ];
    }
}
