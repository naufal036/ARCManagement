<?php

namespace App\Services;

use App\Models\VersiDokumen;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DokumenFileService
{
    private const PENDING_CLEANUP_FILE = 'dms-cleanup-pending.jsonl';

    private const MANIFEST_LOCK_FILE = 'dms-cleanup-pending.lock';

    public function cleanup(string $diskName, array $paths, int $attempts = 3): bool
    {
        $paths = array_values(array_unique(array_filter($paths, 'is_string')));

        if (! $this->pathsAreSafe($diskName, $paths)) {
            Log::warning('Cleanup file dokumen ditolak karena disk atau path tidak valid.', [
                'disk' => $diskName,
                'paths' => $paths,
            ]);

            return false;
        }

        if ($this->deletePaths($diskName, $paths, $attempts)) {
            return true;
        }

        $this->recordPendingCleanup($diskName, $paths);

        return false;
    }

    /**
     * Retry pending entries without holding the manifest lock while storage
     * operations run. The final locked reconciliation preserves new entries.
     */
    public function retryPendingCleanup(int $attempts = 3): int
    {
        $records = $this->withManifestLock(function (): ?array {
            return $this->readManifestLocked();
        });

        if ($records === null || $records === []) {
            return 0;
        }

        $cleanedIds = [];

        foreach ($records as $record) {
            if ($record['entry'] === null) {
                Log::error('Baris manifest cleanup dokumen tidak valid dan dipertahankan.', [
                    'raw' => $record['raw'],
                ]);

                continue;
            }

            $entry = $record['entry'];
            if (! $this->manifestEntryIsSafe($entry)) {
                Log::error('Entri manifest cleanup dokumen tidak aman dan dipertahankan.', [
                    'entry' => $entry,
                ]);

                continue;
            }

            if ($this->deletePaths($entry['disk'], $entry['paths'], $attempts)) {
                $cleanedIds[] = $record['id'];
            }
        }

        if ($cleanedIds === []) {
            return 0;
        }

        $reconciled = $this->withManifestLock(function () use ($cleanedIds): bool {
            $currentRecords = $this->readManifestLocked();

            if ($currentRecords === null) {
                return false;
            }

            $kept = array_values(array_filter(
                $currentRecords,
                fn (array $record): bool => ! in_array($record['id'], $cleanedIds, true)
            ));

            return $this->writeManifestLocked($kept);
        }, false);

        return $reconciled ? count($cleanedIds) : 0;
    }

    /**
     * Returns 0 for a valid file, 404 when it is missing, and 409 when its
     * content cannot be trusted against the recorded SHA-256 checksum.
     */
    public function verifyChecksum(VersiDokumen $version): int
    {
        $diskName = $version->disk ?: 'local';

        if ($diskName !== 'local' || ! $this->pathsAreSafe($diskName, [$version->path_file])) {
            Log::warning('Akses file dokumen ditolak karena disk atau path bukan private DMS.', [
                'version_id' => $version->getKey(),
                'disk' => $diskName,
                'path' => $version->path_file,
            ]);

            return 404;
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($version->path_file)) {
            Log::warning('File dokumen tidak ditemukan saat integritas diperiksa.', [
                'version_id' => $version->getKey(),
                'path' => $version->path_file,
            ]);

            return 404;
        }

        $stream = null;

        try {
            $stream = $disk->readStream($version->path_file);

            if (! is_resource($stream)) {
                throw new \RuntimeException('Stream file tidak dapat dibuka.');
            }

            $context = hash_init('sha256');
            while (! feof($stream)) {
                $chunk = fread($stream, 1024 * 1024);

                if ($chunk === false) {
                    throw new \RuntimeException('Pembacaan stream file gagal.');
                }

                if ($chunk !== '') {
                    hash_update($context, $chunk);
                }
            }

            $actualChecksum = hash_final($context);

            if ($version->checksum_sha256 !== null && ! hash_equals($version->checksum_sha256, $actualChecksum)) {
                Log::warning('Checksum file dokumen tidak cocok.', [
                    'version_id' => $version->getKey(),
                    'path' => $version->path_file,
                ]);

                return 409;
            }

            return 0;
        } catch (Throwable $exception) {
            Log::error('Pemeriksaan checksum file dokumen gagal.', [
                'version_id' => $version->getKey(),
                'path' => $version->path_file,
                'exception' => $exception,
            ]);

            return 409;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    protected function deletePaths(string $diskName, array $paths, int $attempts): bool
    {
        $disk = Storage::disk($diskName);
        $attempts = max(1, $attempts);

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $remaining = array_values(array_filter(
                    $paths,
                    fn (string $path): bool => $disk->exists($path)
                ));

                if ($remaining === []) {
                    return true;
                }

                $disk->delete($remaining);
                $paths = array_values(array_filter(
                    $remaining,
                    fn (string $path): bool => $disk->exists($path)
                ));

                if ($paths === []) {
                    return true;
                }

                Log::warning('Pembersihan file dokumen belum selesai.', [
                    'disk' => $diskName,
                    'attempt' => $attempt,
                    'remaining_paths' => $paths,
                ]);
            } catch (Throwable $exception) {
                Log::warning('Pembersihan file dokumen gagal.', [
                    'disk' => $diskName,
                    'attempt' => $attempt,
                    'paths' => $paths,
                    'exception' => $exception,
                ]);
            }

            if ($attempt < $attempts) {
                usleep(10_000 * $attempt);
            }
        }

        Log::error('Pembersihan file dokumen gagal setelah seluruh percobaan.', [
            'disk' => $diskName,
            'remaining_paths' => $paths,
        ]);

        return false;
    }

    private function recordPendingCleanup(string $diskName, array $paths): bool
    {
        return (bool) $this->withManifestLock(function () use ($diskName, $paths): bool {
            try {
                $handle = fopen($this->manifestPath(), 'ab');

                if ($handle === false) {
                    throw new \RuntimeException('Manifest cleanup tidak dapat dibuka.');
                }

                try {
                    $line = json_encode([
                        'id' => bin2hex(random_bytes(16)),
                        'disk' => $diskName,
                        'paths' => array_values($paths),
                        'recorded_at' => now()->toIso8601String(),
                    ], JSON_THROW_ON_ERROR).PHP_EOL;

                    if (fwrite($handle, $line) === false) {
                        throw new \RuntimeException('Manifest cleanup tidak dapat ditulis.');
                    }

                    fflush($handle);
                } finally {
                    fclose($handle);
                }

                return true;
            } catch (Throwable $exception) {
                Log::critical('Registry cleanup dokumen tidak dapat ditulis.', [
                    'disk' => $diskName,
                    'paths' => $paths,
                    'exception' => $exception,
                ]);

                return false;
            }
        }, false);
    }

    private function readManifestLocked(): ?array
    {
        $manifestPath = $this->manifestPath();

        if (! is_file($manifestPath)) {
            return [];
        }

        $lines = file($manifestPath, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            Log::error('Manifest cleanup dokumen tidak dapat dibaca.', ['path' => $manifestPath]);

            return null;
        }

        return array_map(function (string $raw): array {
            $entry = json_decode($raw, true);

            if (! is_array($entry) || json_last_error() !== JSON_ERROR_NONE) {
                return ['id' => hash('sha256', $raw), 'raw' => $raw, 'entry' => null];
            }

            return [
                'id' => is_string($entry['id'] ?? null) ? $entry['id'] : hash('sha256', $raw),
                'raw' => $raw,
                'entry' => $entry,
            ];
        }, $lines);
    }

    private function writeManifestLocked(array $records): bool
    {
        $manifestPath = $this->manifestPath();

        if ($records === []) {
            if (is_file($manifestPath) && ! unlink($manifestPath)) {
                Log::error('Manifest cleanup kosong tidak dapat dihapus.', ['path' => $manifestPath]);

                return false;
            }

            return true;
        }

        $temporaryPath = $manifestPath.'.tmp.'.bin2hex(random_bytes(8));

        try {
            $content = implode(PHP_EOL, array_map(
                fn (array $record): string => $record['raw'],
                $records
            )).PHP_EOL;

            if (file_put_contents($temporaryPath, $content, LOCK_EX) === false) {
                throw new \RuntimeException('Manifest cleanup tidak dapat diperbarui.');
            }

            if (! $this->replaceManifestFile($temporaryPath, $manifestPath)) {
                throw new \RuntimeException('Manifest cleanup tidak dapat diganti.');
            }

            return true;
        } catch (Throwable $exception) {
            Log::error('Manifest cleanup tidak dapat diperbarui.', [
                'path' => $manifestPath,
                'exception' => $exception,
            ]);

            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            return false;
        }
    }

    private function withManifestLock(Closure $callback, mixed $default = null): mixed
    {
        $handle = null;
        $locked = false;

        try {
            $handle = fopen($this->lockPath(), 'c');

            if ($handle === false) {
                throw new \RuntimeException('Lock manifest cleanup tidak dapat diperoleh.');
            }

            if (! flock($handle, LOCK_EX)) {
                throw new \RuntimeException('Lock manifest cleanup tidak dapat diperoleh.');
            }

            $locked = true;

            return $callback();
        } catch (Throwable $exception) {
            Log::error('Operasi manifest cleanup tidak dapat dikunci.', [
                'exception' => $exception,
            ]);

            return $default;
        } finally {
            if (is_resource($handle)) {
                if ($locked) {
                    flock($handle, LOCK_UN);
                }

                fclose($handle);
            }
        }
    }

    private function pathsAreSafe(string $diskName, array $paths): bool
    {
        if ($diskName !== 'local' || $paths === [] || count($paths) !== count(array_unique($paths))) {
            return false;
        }

        foreach ($paths as $path) {
            if (! is_string($path)
                || $path === ''
                || str_contains($path, "\0")
                || str_contains($path, '\\')
                || str_starts_with($path, '/')
                || preg_match('/(^|\/)\.\.?(\/|$)/', $path)
                || ! preg_match('/^dokumen-proyek\/[1-9][0-9]*\/[A-Za-z0-9._-]+$/D', $path)) {
                return false;
            }
        }

        return true;
    }

    private function replaceManifestFile(string $temporaryPath, string $manifestPath): bool
    {
        if (@rename($temporaryPath, $manifestPath)) {
            return true;
        }

        $backupPath = $manifestPath.'.bak.'.bin2hex(random_bytes(8));
        $hadExistingManifest = is_file($manifestPath);

        try {
            if ($hadExistingManifest && ! @rename($manifestPath, $backupPath)) {
                return false;
            }

            if (! @rename($temporaryPath, $manifestPath)) {
                if ($hadExistingManifest && is_file($backupPath)) {
                    @rename($backupPath, $manifestPath);
                }

                return false;
            }

            if ($hadExistingManifest && is_file($backupPath)) {
                @unlink($backupPath);
            }

            return true;
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }

    private function manifestEntryIsSafe(array $entry): bool
    {
        return isset($entry['disk'], $entry['paths'])
            && is_string($entry['disk'])
            && is_array($entry['paths'])
            && $this->pathsAreSafe($entry['disk'], $entry['paths']);
    }

    private function manifestPath(): string
    {
        return Storage::disk('local')->path(self::PENDING_CLEANUP_FILE);
    }

    private function lockPath(): string
    {
        return Storage::disk('local')->path(self::MANIFEST_LOCK_FILE);
    }
}
