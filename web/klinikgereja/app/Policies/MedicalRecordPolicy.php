<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
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
        return $user->hasRole(['Admin', 'Dokter', 'Perawat', 'Bendahara']);
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Dokter', 'Perawat']);
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasRole(['Admin', 'Dokter']);
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasRole(['Admin', 'Super Admin']);
    }
}
