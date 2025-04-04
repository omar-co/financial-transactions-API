<?php

namespace App\Actions\Account;

use App\Actions\Action;
use App\Models\Account;
use App\Models\User;

class CreateAccountAction implements Action
{
    public function execute(User $user): Account
    {
        $account = Account::create([
            'user_id' => $user->id,
            'balance' => money(0),
        ]);

        return $account->setRelation('user', $user);
    }
}
