<?php

namespace App\Enums\Account;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdraw = 'withdraw';
}
