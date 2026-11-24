<?php

namespace App\Policies;

use App\Models\DokumenProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;

class DokumenProyekPolicy
{
    public function before(User $user, string $ability): ?bool
    {
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

    private function hasProjectAccess(User $user, int $projectId): bool
    {
        return ProjectAssignment::query()
            ->where('project_id', $projectId)
            ->where('user_id', $user->getKey())
            ->exists();
    }
}
