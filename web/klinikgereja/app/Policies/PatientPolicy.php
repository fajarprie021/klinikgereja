<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * MVP: pasien dikelola oleh Admin Klinik.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('Admin');
    }
}
