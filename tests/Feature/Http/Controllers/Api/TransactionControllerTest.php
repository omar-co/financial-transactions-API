<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\Account\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_401_when_user_is_not_authenticated(): void
    {
        $account = Account::factory()->create();
        $route = route('accounts.transactions.store', $account);

        $payload = [
            'amount' => 5000,
            'type' => TransactionType::Deposit->value,
        ];

        $this->postJson($route, $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_returns_403_when_user_is_not_authorized_for_account(): void
    {
        $otherUserAccount = Account::factory()->for(User::factory())->create();
        $route = route('accounts.transactions.store', $otherUserAccount);

        $user = User::factory()->create();
        Passport::actingAs($user);

        $payload = [
            'amount' => 5000,
            'type' => TransactionType::Deposit->value,
        ];

        $this->postJson($route, $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function store_fails_with_422_on_validation_errors(): void
    {
        [$user, $account] = $this->createUserAndAccount();
        $route = route('accounts.transactions.store', $account);

        // missing 'amount'
        $this->postJson($route, ['type' => TransactionType::Deposit->value])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('amount');

        // 'amount' is not positive
        $this->postJson($route, ['amount' => 0, 'type' => TransactionType::Deposit->value])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('amount');

        $this->postJson($route, ['amount' => -100, 'type' => TransactionType::Deposit->value])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('amount');

        // missing 'type'
        $this->postJson($route, ['amount' => 1000])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('type');

        // invalid 'type'
        $this->postJson($route, ['amount' => 1000, 'type' => 'invalid_type'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('type');
    }

    #[Test]
    public function it_successfully_creates_deposit_transaction(): void
    {
        $initialBalance = 10000;
        $depositAmount = 5000;
        $expectedBalance = $initialBalance + $depositAmount;

        [$user, $account] = $this->createUserAndAccount($initialBalance);
        $route = route('accounts.transactions.store', $account);

        $payload = [
            'amount' => $depositAmount,
            'type' => TransactionType::Deposit->value,
        ];

        $response = $this->postJson($route, $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'amount' => ['amount', 'currency', 'formatted'],
                    'balance_before' => ['amount', 'currency', 'formatted'],
                    'balance_after' => ['amount', 'currency', 'formatted'],
                    'created_at',
                ]
            ])
            ->assertJson(
                fn (AssertableJson $json) => $json->where('data.type', TransactionType::Deposit->value)
                    ->where('data.amount.amount', (string)$depositAmount)
                    ->where('data.balance_before.amount', (string)$initialBalance)
                    ->where('data.balance_after.amount', (string)$expectedBalance)
                    ->etc()
            );

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => $expectedBalance,
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => TransactionType::Deposit->value,
            'amount' => $depositAmount,
            'balance_before' => $initialBalance,
            'balance_after' => $expectedBalance,
        ]);
    }

    #[Test]
    public function it_successfully_creates_withdrawal_transaction(): void
    {
        $initialBalance = 10000;
        $withdrawalAmount = 3000;
        $expectedBalance = $initialBalance - $withdrawalAmount;

        [$user, $account] = $this->createUserAndAccount($initialBalance);
        $route = route('accounts.transactions.store', $account);

        $payload = [
            'amount' => $withdrawalAmount,
            'type' => TransactionType::Withdraw->value,
        ];

        $response = $this->postJson($route, $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => [
                'id',
                'type',
                'amount' => ['amount', 'currency', 'formatted'],
                'balance_before' => ['amount', 'currency', 'formatted'],
                'balance_after' => ['amount', 'currency', 'formatted'],
                'created_at',
            ]])
            ->assertJson(
                fn (AssertableJson $json) => $json->where('data.type', TransactionType::Withdraw->value)
                    ->where('data.amount.amount', (string)$withdrawalAmount)
                    ->where('data.balance_before.amount', (string)$initialBalance)
                    ->where('data.balance_after.amount', (string)$expectedBalance)
                    ->etc()
            );

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => $expectedBalance,
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => TransactionType::Withdraw->value,
            'amount' => $withdrawalAmount,
            'balance_before' => $initialBalance,
            'balance_after' => $expectedBalance,
        ]);
    }

    #[Test]
    public function it_returns_422_due_to_insufficient_funds(): void
    {
        $initialBalance = 1000;
        $withdrawalAmount = 2000;

        [$user, $account] = $this->createUserAndAccount($initialBalance);
        $route = route('accounts.transactions.store', $account);

        $payload = [
            'amount' => $withdrawalAmount,
            'type' => TransactionType::Withdraw->value,
        ];

        $response = $this->postJson($route, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'Insufficient funds for this operation.'
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => $initialBalance,
        ]);

        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'type' => TransactionType::Withdraw->value,
            'amount' => $withdrawalAmount,
        ]);
    }

    #[Test]
    public function it_returns_401_when_user_is_not_authenticated_and_request_account_transactions(): void
    {
        $account = Account::factory()->create();
        $route = route('accounts.transactions.index', $account);

        $this->postJson($route)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_returns_403_when_the_account_belongs_to_another_user_and_request_account_transactions(): void
    {
        [$user, $account] = $this->createUserAndAccount();

        $forbiddenAccount = Account::factory()->for(User::factory())->create();
        $route = route('accounts.transactions.index', $forbiddenAccount);

        $this->getJson($route)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function it_returns_the_account_transactions(): void
    {
        [$user, $account] = $this->createUserAndAccount();

        $transactions = Transaction::factory(25)->for($account)->create();
        $otherTransactions = Transaction::factory(15)->for(Account::factory()->for(User::factory()))->create();

        $route = route('accounts.transactions.index', $account);

        $response = $this->getJson($route)
            ->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonPath('meta.total', 25)
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    15,
                    fn (AssertableJson $json) => $json->where('account_id', $account->id)
                ->etc()
                )->etc()
            );

    }

    protected function createUserAndAccount(int $initialBalance = 10000): array
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => $initialBalance,
        ]);
        Passport::actingAs($user);
        return [$user, $account];
    }
}
