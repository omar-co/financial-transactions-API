<?php

namespace App\Processes\Account;

use App\Processes\Account\Pipes\MakeWithdrawPipe;
use App\Processes\Account\Pipes\RecordTransactionPipe;
use App\Processes\Account\Pipes\ValidateWithdrawAmountPipe;
use App\Processes\Process;

class WithdrawProcess extends Process
{
    protected function pipes(): array
    {
        return [
            ValidateWithdrawAmountPipe::class,
            MakeWithdrawPipe::class,
            RecordTransactionPipe::class,
        ];
    }
}
