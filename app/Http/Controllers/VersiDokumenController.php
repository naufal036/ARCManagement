<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\VersiDokumen;
use App\Services\DokumenFileService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VersiDokumenController extends Controller
{
    private const MAX_BATCH_BYTES = 104857600;

    private const ALLOWED_EXTENSIONS = [
        'pdf', 'dwg', 'dxf', 'doc', 'docx', 'xls', 'xlsx',
        'png', 'jpg', 'jpeg', 'zip', 'rar', 'skp', 'ifc',
    ];

    private const PREVIEW_MIMES = ['application/pdf', 'image/png', 'image/jpeg'];

    private const MIME_TYPES = [
        'pdf' => ['application/pdf'],
        'dwg' => ['application/acad', 'application/octet-stream', 'application/x-dwg', 'image/vnd.dwg'],
        'dxf' => ['application/dxf', 'application/octet-stream', 'image/vnd.dxf', 'text/plain'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'png' => ['image/png'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/vnd.rar', 'application/x-rar-compressed', 'application/octet-stream'],
        'skp' => ['application/octet-stream', 'application/vnd.sketchup.skp'],
        'ifc' => ['application/ifc', 'application/octet-stream', 'text/plain'],
    ];

    private const MAGIC_PREFIXES = [
        'pdf' => ['%PDF-'],
        'png' => ["\x89PNG\r\n\x1a\n"],
        'jpg' => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'zip' => ['PK'],
        'docx' => ['PK'],
        'xlsx' => ['PK'],
        'rar' => ['Rar!'],
    ];

    public function __construct(private readonly DokumenFileService $fileService) {}

    public function store(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        Gate::authorize('uploadVersion', $dokumenProyek);

        $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'max:51200'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $files = $request->file('files', []);
        $totalBytes = array_sum(array_map(
            fn (UploadedFile $file): int => (int) $file->getSize(),
            $files
        ));

        if ($totalBytes > self::MAX_BATCH_BYTES) {
            throw ValidationException::withMessages([
                'files' => 'Ukuran total batch tidak boleh melebihi 100 MB.',
            ]);
        }

        $storedPaths = [];
        $completed = false;
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts && ! $completed; $attempt++) {
            $storedPaths = [];

            try {
                DB::transaction(function () use ($files, $request, $dokumenProyek, &$storedPaths): void {
                    $lockedDocument = DokumenProyek::query()
                        ->whereKey($dokumenProyek->getKey())
                        ->lockForUpdate()
                        ->firstOrFail();
                    $nextVersion = ((int) $lockedDocument->versiDokumen()->max('nomor_versi')) + 1;

                    foreach ($files as $file) {
                        $metadata = $this->validateDocumentFile($file);
                        $storageName = Str::uuid().'.'.$metadata['extension'];
                        $path = $file->storeAs('dokumen-proyek/'.$lockedDocument->getKey(), $storageName, 'local');

                        if ($path === false) {
                            throw new \RuntimeException('File dokumen gagal disimpan.');
                        }

                        $storedPaths[] = $path;
                        $lockedDocument->versiDokumen()->create([
                            'nomor_versi' => $nextVersion++,
                            'nama_file' => Str::substr(basename($file->getClientOriginalName()), 0, 255),
                            'nama_file_asli' => Str::substr(basename($file->getClientOriginalName()), 0, 255),
                            'nama_file_storage' => $storageName,
                            'path_file' => $path,
                            'disk' => 'local',
                            'mime_type' => $metadata['mime_type'],
                            'ekstensi' => $metadata['extension'],
                            'ukuran_file' => $metadata['size'],
                            'checksum_sha256' => $metadata['checksum'],
                            'catatan' => $request->input('catatan'),
                            'user_id' => $request->user()->getKey(),
                        ]);
                    }
                });

                $completed = true;
            } catch (QueryException $exception) {
                $this->fileService->cleanup('local', $storedPaths);

                if (! $this->isDuplicateVersionException($exception) || $attempt === $maxAttempts) {
                    throw $exception;
                }

                usleep(20_000 * $attempt);
            } catch (\Throwable $exception) {
                $this->fileService->cleanup('local', $storedPaths);
                throw $exception;
            }
        }

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('success', count($files).' file berhasil diunggah sebagai versi dokumen.');
    }

    public function view(VersiDokumen $versiDokumen)
    {
        $versiDokumen->loadMissing('dokumenProyek');
        abort_unless($versiDokumen->dokumenProyek !== null, 404);
        Gate::authorize('downloadVersion', $versiDokumen->dokumenProyek);

        abort_unless(in_array($versiDokumen->mime_type, self::PREVIEW_MIMES, true), 404);

        $this->assertFileIntegrity($versiDokumen);
        $disk = Storage::disk('local');

        return response()->file($disk->path($versiDokumen->path_file), [
            'Content-Type' => $versiDokumen->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.$this->safeFileName($versiDokumen->nama_file_asli).'"',
        ]);
    }

    public function download(VersiDokumen $versiDokumen): StreamedResponse
    {
        $versiDokumen->loadMissing('dokumenProyek');
        abort_unless($versiDokumen->dokumenProyek !== null, 404);
        Gate::authorize('downloadVersion', $versiDokumen->dokumenProyek);

        $this->assertFileIntegrity($versiDokumen);
        $disk = Storage::disk('local');

        $downloadName = $this->safeFileName($versiDokumen->nama_file_asli);

        return $disk->download($versiDokumen->path_file, $downloadName, [
            'Content-Type' => 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function validateDocumentFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages(['files' => 'Format file tidak didukung.']);
        }

        if (! in_array($mimeType, self::MIME_TYPES[$extension], true)) {
            throw ValidationException::withMessages(['files' => 'MIME type tidak sesuai dengan ekstensi file.']);
        }

        $prefix = file_get_contents($file->getRealPath(), false, null, 0, 16) ?: '';
        if (str_starts_with($prefix, 'MZ') || str_starts_with($prefix, "\x7FELF") || str_starts_with($prefix, '#!')) {
            throw ValidationException::withMessages(['files' => 'File executable tidak diizinkan.']);
        }

        $magicValid = false;
        foreach (self::MAGIC_PREFIXES[$extension] ?? [] as $magicPrefix) {
            if (str_starts_with($prefix, $magicPrefix)) {
                $magicValid = true;
                break;
            }
        }

        if (isset(self::MAGIC_PREFIXES[$extension]) && ! $magicValid) {
            throw ValidationException::withMessages(['files' => 'Signature file tidak sesuai dengan ekstensi file.']);
        }

        return [
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size' => (int) $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
        ];
    }

    private function safeFileName(string $name): string
    {
        $name = basename(str_replace(["\r", "\n"], '', $name));

        return preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?: 'dokumen';
    }

    private function assertFileIntegrity(VersiDokumen $version): void
    {
        $status = $this->fileService->verifyChecksum($version);

        abort_if($status === 404, 404);
        abort_if($status === 409, 409, 'Integritas file dokumen tidak dapat diverifikasi.');
    }

    private function isDuplicateVersionException(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'nomor_versi')
            || str_contains($message, 'dokumen_proyek_id_nomor_versi_unique');
    }
}
