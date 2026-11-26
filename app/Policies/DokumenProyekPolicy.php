<?php

namespace App\Policies;

use App\Enums\DokumenVerificationStatus;
use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;

class DokumenProyekPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['submitForReview', 'review', 'finalize'], true)) {
            return null;
        }

        return in_array($user->role?->nama_role, ['Admin', 'Direktur'], true) ? true : null;
    }

    public function view(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $this->hasProjectAccess($user, $dokumenProyek->proyek_id);
    }

    public function create(User $user, Proyek $proyek): bool
    {
        return $this->hasProjectAccess($user, $proyek->getKey());
    }

    public function update(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $this->hasProjectAccess($user, $dokumenProyek->proyek_id);
    }

    public function delete(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $this->hasProjectAccess($user, $dokumenProyek->proyek_id);
    }

    public function uploadVersion(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $this->hasProjectAccess($user, $dokumenProyek->proyek_id);
    }

    public function downloadVersion(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $this->hasProjectAccess($user, $dokumenProyek->proyek_id);
    }

    public function submitForReview(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $user->role?->nama_role === 'Staf Teknis'
            && $this->hasProjectAccess($user, $dokumenProyek->proyek_id)
            && in_array($dokumenProyek->status_verifikasi, [
                DokumenVerificationStatus::Draft,
                DokumenVerificationStatus::Rejected,
                DokumenVerificationStatus::RevisionRequired,
            ], true);
    }

    public function review(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $user->role?->nama_role === 'Pengawas Lapangan'
            && $this->hasProjectAccess($user, $dokumenProyek->proyek_id)
            && $dokumenProyek->status_verifikasi === DokumenVerificationStatus::PendingReview
            && ! ActivityLog::query()
                ->where('subject_type', class_basename(DokumenProyek::class))
                ->where('subject_id', $dokumenProyek->getKey())
                ->whereIn('activity', [
                    'document_review_pass',
                    'document_review_revision_required',
                    'document_review_rejected',
                ])
                ->where('properties->verification_cycle', $dokumenProyek->verification_cycle)
                ->exists();
    }

    public function finalize(User $user, DokumenProyek $dokumenProyek): bool
    {
        return $user->role?->nama_role === 'Direktur'
            && $dokumenProyek->status_verifikasi === DokumenVerificationStatus::PendingReview;
    }

    private function hasProjectAccess(User $user, int $projectId): bool
    {
        return ProjectAssignment::query()
            ->where('project_id', $projectId)
            ->where('user_id', $user->getKey())
            ->exists();
    }
}
