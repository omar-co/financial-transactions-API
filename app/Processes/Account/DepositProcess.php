<?php

namespace App\Processes\Account;

use App\Processes\Account\Pipes\MakeDepositPipe;
use App\Processes\Account\Pipes\RecordTransactionPipe;
use App\Processes\Account\Pipes\ValidateDepositAmountPipe;
use App\Processes\Process;

class DepositProcess extends Process
{
    protected function pipes(): array
    {
        return [
            ValidateDepositAmountPipe::class,
            MakeDepositPipe::class,
            RecordTransactionPipe::class,
        ];
    }
}
