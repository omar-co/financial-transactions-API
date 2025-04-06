<?php

namespace App\Processes\Account\Pipes;

use App\Dtos\Account\TransactionDto;
use App\Processes\Account\Errors\InsufficientFundsException;
use App\Queries\UserAccountsQuery;
use Illuminate\Support\Facades\DB;

class MakeWithdrawPipe
{
    public function __invoke(TransactionDto $payload, \Closure $next)
    {
        DB::transaction(function () use ($payload) {

            $account = UserAccountsQuery::query()
                ->accountForUpdate($payload->accountId)
                ->firstOrFail();

            if ($payload->amount->greaterThan($account->balance)) {
                throw new InsufficientFundsException('Insufficient funds for this operation.');
            }

            $payload->balanceBefore = $account->balance;
            $payload->balanceAfter = $payload->balanceBefore->subtract($payload->amount);

            $account->balance = $payload->balanceAfter;

            $account->save();

        });

        return $next($payload);

    }
}
