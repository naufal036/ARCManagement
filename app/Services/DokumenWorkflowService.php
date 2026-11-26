<?php

namespace App\Services;

use App\Enums\DokumenVerificationStatus;
use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DokumenWorkflowService
{
    public function submitForReview(DokumenProyek $dokumenProyek, int $lockVersion): void
    {
        DB::transaction(function () use ($dokumenProyek, $lockVersion): void {
            $document = $this->lockDocument($dokumenProyek);
            Gate::authorize('submitForReview', $document);
            $this->assertCurrentVersion($document, $lockVersion);

            if (! $document->versiDokumen()->exists()) {
                throw ValidationException::withMessages([
                    'workflow' => 'Unggah setidaknya satu file sebelum mengajukan dokumen.',
                ]);
            }

            $beforeStatus = $document->status_verifikasi->value;
            $document->status_verifikasi = DokumenVerificationStatus::PendingReview;
            $document->verification_cycle++;
            $document->lock_version++;
            $document->save();

            ActivityLog::record(
                'document_submitted_for_review',
                'Dokumen diajukan untuk pemeriksaan Pengawas Lapangan.',
                $document,
                properties: [
                    'project_id' => $document->proyek_id,
                    'category_id' => $document->kategori_dokumen_id,
                    'verification_cycle' => $document->verification_cycle,
                    'before' => ['status_verifikasi' => $beforeStatus],
                    'after' => ['status_verifikasi' => $document->status_verifikasi->value],
                ]
            );
        });
    }

    public function review(
        DokumenProyek $dokumenProyek,
        int $lockVersion,
        string $decision,
        ?string $note
    ): void {
        DB::transaction(function () use ($dokumenProyek, $lockVersion, $decision, $note): void {
            $document = $this->lockDocument($dokumenProyek);
            Gate::authorize('review', $document);
            $this->assertCurrentVersion($document, $lockVersion);

            $nextStatus = match ($decision) {
                'pass' => DokumenVerificationStatus::PendingReview,
                'revision_required' => DokumenVerificationStatus::RevisionRequired,
                'rejected' => DokumenVerificationStatus::Rejected,
            };
            $beforeStatus = $document->status_verifikasi->value;
            $document->status_verifikasi = $nextStatus;
            $document->lock_version++;
            $document->save();

            ActivityLog::record(
                'document_review_'.$decision,
                $note ?: 'Pengawas Lapangan menyelesaikan pemeriksaan dokumen.',
                $document,
                properties: [
                    'project_id' => $document->proyek_id,
                    'category_id' => $document->kategori_dokumen_id,
                    'verification_cycle' => $document->verification_cycle,
                    'decision' => $decision,
                    'note' => $note,
                    'before' => ['status_verifikasi' => $beforeStatus],
                    'after' => ['status_verifikasi' => $document->status_verifikasi->value],
                ]
            );
        });
    }

    public function finalize(
        DokumenProyek $dokumenProyek,
        int $lockVersion,
        string $decision,
        ?string $note
    ): void {
        DB::transaction(function () use ($dokumenProyek, $lockVersion, $decision, $note): void {
            $document = $this->lockDocument($dokumenProyek);
            Gate::authorize('finalize', $document);
            $this->assertCurrentVersion($document, $lockVersion);

            $reviewPassed = ActivityLog::query()
                ->where('subject_type', class_basename(DokumenProyek::class))
                ->where('subject_id', $document->getKey())
                ->where('activity', 'document_review_pass')
                ->where('properties->verification_cycle', $document->verification_cycle)
                ->exists();

            if (! $reviewPassed) {
                abort(409, 'Pemeriksaan Pengawas Lapangan harus lolos sebelum keputusan Direktur.');
            }

            $nextStatus = match ($decision) {
                'approved' => DokumenVerificationStatus::Approved,
                'revision_required' => DokumenVerificationStatus::RevisionRequired,
                'rejected' => DokumenVerificationStatus::Rejected,
            };
            $beforeStatus = $document->status_verifikasi->value;
            $document->status_verifikasi = $nextStatus;
            $document->lock_version++;
            $document->save();

            ActivityLog::record(
                'document_final_'.$decision,
                $note ?: 'Direktur memberikan keputusan akhir atas dokumen.',
                $document,
                properties: [
                    'project_id' => $document->proyek_id,
                    'category_id' => $document->kategori_dokumen_id,
                    'verification_cycle' => $document->verification_cycle,
                    'decision' => $decision,
                    'note' => $note,
                    'before' => ['status_verifikasi' => $beforeStatus],
                    'after' => ['status_verifikasi' => $document->status_verifikasi->value],
                ]
            );
        });
    }

    private function lockDocument(DokumenProyek $dokumenProyek): DokumenProyek
    {
        return DokumenProyek::query()
            ->whereKey($dokumenProyek->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function assertCurrentVersion(DokumenProyek $document, int $lockVersion): void
    {
        if ($document->lock_version !== $lockVersion) {
            abort(409, 'Dokumen telah diubah oleh pengguna lain. Muat ulang halaman sebelum melanjutkan.');
        }
    }
}
