<?php

namespace App\Processes\Account\Pipes;

use App\Dtos\Account\TransactionDto;
use App\Queries\UserAccountsQuery;
use Illuminate\Support\Facades\DB;

class MakeDepositPipe
{
    public function __invoke(TransactionDto $payload, \Closure $next)
    {
        DB::transaction(function () use ($payload) {

            $account = UserAccountsQuery::query()
                ->accountForUpdate($payload->accountId)
                ->firstOrFail();

            $payload->balanceBefore = $account->balance;
            $payload->balanceAfter = $payload->balanceBefore->add($payload->amount);

            $account->balance = $payload->balanceAfter;

            $account->save();

        });

        return $next($payload);

    }
}
