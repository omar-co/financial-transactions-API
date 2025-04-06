<?php

namespace App\Queries;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

/** @mixin Builder */
class TransactionsQuery extends Query
{
    public function __construct()
    {
        $this->builder = Transaction::query();
    }

    public static function for(Account $account): self
    {
        return (new self())
            ->whereBelongsTo($account);
    }


}
