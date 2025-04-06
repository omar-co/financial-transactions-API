<?php

namespace App\Processes\Account\Errors;

class InsufficientFundsException extends \Exception implements TransactionException
{
}
