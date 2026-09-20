<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
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

    public function view(User $user, Transaction $transaction): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Bendahara', 'Admin']);
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasRole(['Bendahara', 'Admin']);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('Super Admin');
    }
}
