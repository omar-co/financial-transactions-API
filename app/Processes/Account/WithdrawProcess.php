<?php

namespace App\Processes\Account;

use App\Lib\Process\Process;
use App\Processes\Account\Pipes\MakeWithdrawPipe;
use App\Processes\Account\Pipes\RecordTransactionPipe;
use App\Processes\Account\Pipes\ValidateWithdrawAmountPipe;

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
