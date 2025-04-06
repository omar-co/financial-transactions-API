<?php

namespace App\Dtos\Account;

use App\Enums\Account\TransactionType;
use App\Models\Transaction;
use Cknow\Money\Money;

class TransactionDto
{
    public function __construct(
        public int $accountId,
        public Money $amount,
        public TransactionType $type,
        public ?Money $balanceBefore  = null,
        public ?Money $balanceAfter = null,
        public ?Transaction $transaction = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'account_id' => $this->accountId,
            'amount' => $this->amount,
            'type' => $this->type->value,
            'balance_before' => $this->balanceBefore,
            'balance_after' => $this->balanceAfter,
        ];
    }
}
