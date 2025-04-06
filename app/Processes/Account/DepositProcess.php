<?php

namespace App\Processes\Account;

use App\Lib\Process\Process;
use App\Processes\Account\Pipes\MakeDepositPipe;
use App\Processes\Account\Pipes\RecordTransactionPipe;
use App\Processes\Account\Pipes\ValidateDepositAmountPipe;

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
