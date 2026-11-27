<?php

namespace App\Policies;

use App\Models\LaporanPengawasan;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;

class LaporanPengawasanPolicy
{
    public function before(User $user): ?bool { return in_array($user->role?->name, ['Admin', 'Direktur'], true) ? true : null; }
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, LaporanPengawasan $laporanPengawasan): bool { return $this->hasProjectAccess($user, $laporanPengawasan->proyek); }
    public function create(User $user, Proyek $proyek): bool { return $this->hasProjectAccess($user, $proyek); }
    public function update(User $user, LaporanPengawasan $laporanPengawasan): bool { return $this->hasProjectAccess($user, $laporanPengawasan->proyek); }
    public function delete(User $user, LaporanPengawasan $laporanPengawasan): bool { return $this->hasProjectAccess($user, $laporanPengawasan->proyek); }

    private function hasProjectAccess(User $user, ?Proyek $proyek): bool
    {
        return $proyek?->project_type === 'Pengawasan'
            && ProjectAssignment::query()->where('project_id', $proyek->id)->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LaporanPengawasan $laporanPengawasan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LaporanPengawasan $laporanPengawasan): bool
    {
        return false;
    }
}
