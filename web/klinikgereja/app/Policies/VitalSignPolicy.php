<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VitalSign;

class VitalSignPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Dokter', 'Perawat']);
    }

    public function view(User $user, VitalSign $vitalSign): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Dokter', 'Perawat']);
    }

    public function update(User $user, VitalSign $vitalSign): bool
    {
        return $user->hasRole(['Admin', 'Dokter', 'Perawat']);
    }

    public function delete(User $user, VitalSign $vitalSign): bool
    {
        return $user->hasRole(['Admin', 'Super Admin']);
    }
}
