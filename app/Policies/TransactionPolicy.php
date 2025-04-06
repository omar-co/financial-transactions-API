<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAccountTransactions(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }
    public function create(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }
}
