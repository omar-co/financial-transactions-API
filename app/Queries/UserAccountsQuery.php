<?php

namespace App\Queries;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** @mixin Builder */
class UserAccountsQuery extends Query
{
    public function __construct()
    {
        $this->builder = Account::query();
    }

    public static function for(User $user): self
    {
        return (new self())
            ->whereBelongsTo($user);
    }

    public static function query(): self
    {
        return new self();
    }

    public function accountForUpdate(int $accountId): self
    {
        return self::query()
        ->where('id', $accountId)
        ->lockForUpdate();
    }
}
