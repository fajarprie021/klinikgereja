<?php

namespace App\Policies;

use App\Models\Subsidy;
use App\Models\User;

class SubsidyPolicy
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
        return $user->hasRole(['Bendahara', 'Admin']);
    }

    public function view(User $user, Subsidy $subsidy): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Bendahara', 'Admin']);
    }

    public function update(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRole('Bendahara');
    }

    public function approve(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRole('Bendahara');
    }

    public function delete(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRole(['Bendahara', 'Super Admin']);
    }
}
