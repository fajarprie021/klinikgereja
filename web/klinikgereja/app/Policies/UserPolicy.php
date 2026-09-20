<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function view(User $user, User $managedUser): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, User $managedUser): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete(User $user, User $managedUser): bool
    {
        return $user->hasRole('Super Admin') && $user->id !== $managedUser->id;
    }
}
