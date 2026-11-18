<?php

namespace App\Policies;

use App\Models\KategoriDokumen;
use App\Models\User;

class KategoriDokumenPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return in_array($user->role?->nama_role, ['Admin', 'Direktur'], true) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, KategoriDokumen $kategoriDokumen): bool
    {
        return false;
    }

    public function delete(User $user, KategoriDokumen $kategoriDokumen): bool
    {
        return false;
    }
}
