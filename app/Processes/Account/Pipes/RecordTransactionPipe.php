<?php

namespace App\Processes\Account\Pipes;

use App\Dtos\Account\TransactionDto;
use App\Models\Transaction;

class RecordTransactionPipe
{
    public function __invoke(TransactionDto $payload, \Closure $next)
    {
        //TODO: add payload validation
        $transaction = Transaction::create($payload->toArray());

        $payload->transaction = $transaction;

        return $next($payload);
    }
}
