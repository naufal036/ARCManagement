<?php

namespace App\Policies;

use App\Models\ProgressProyek;
use App\Models\ProjectAssignment;
use App\Models\User;

class ProgressProyekPolicy
{
    public function uploadDocumentation(User $user, ProgressProyek $progressProyek): bool
    {
        return $this->canAccessProject($user, $progressProyek);
    }

    public function viewDocumentation(User $user, ProgressProyek $progressProyek): bool
    {
        return $this->canAccessProject($user, $progressProyek);
    }

    private function canAccessProject(User $user, ProgressProyek $progressProyek): bool
    {
        if (in_array($user->role?->name, ['Admin', 'Direktur'], true)) {
            return true;
        }

        return ProjectAssignment::query()
            ->where('project_id', $progressProyek->proyek_id)
            ->where('user_id', $user->getKey())
            ->exists();
    }
}
