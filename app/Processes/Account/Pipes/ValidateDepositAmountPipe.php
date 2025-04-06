<?php

namespace App\Processes\Account\Pipes;

use App\Dtos\Account\TransactionDto;
use App\Processes\Account\Errors\InvalidAmountException;
use Closure;

final class ValidateDepositAmountPipe
{
    public function __invoke(TransactionDto $payload, Closure $next): mixed
    {
        if ($payload->amount->isNegative() || $payload->amount->isZero()) {
            throw new InvalidAmountException('Deposit amount must be greater than 0');
        }

        return $next($payload);
    }
}
