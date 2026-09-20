<?php

namespace App\Policies;

use App\Models\ChurchFund;
use App\Models\User;

class ChurchFundPolicy
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

    public function view(User $user, ChurchFund $churchFund): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Bendahara');
    }

    public function update(User $user, ChurchFund $churchFund): bool
    {
        return $user->hasRole('Bendahara');
    }

    public function delete(User $user, ChurchFund $churchFund): bool
    {
        return $user->hasRole('Super Admin');
    }
}
