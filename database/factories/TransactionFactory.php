<?php

namespace Database\Factories;

use App\Enums\Account\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomNumber(),
            'type' => new Sequence(TransactionType::Deposit, TransactionType::Withdraw),
            'balance_before' => $this->faker->randomNumber(),
            'balance_after' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'account_id' => Account::factory(),
        ];
    }
}
