<?php

namespace Http\Controllers\Api;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_401_when_there_is_no_authenticated_user(): void
    {
        $this->postJson(route('accounts.store'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_creates_a_new_account_when_there_is_an_authenticated_user(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson(route('accounts.store'))
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        $response->assertExactJsonStructure([
            'data' => [
                'id',
                'balance' => [
                    'amount',
                    'currency',
                    'formatted',
                ],
                'created_at',
                'user' => [
                    'id',
                    'name',
                    'created_at',
                ]
            ]
        ]);
    }

    #[Test]
    public function it_returns_the_account_details(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->getJson(route('accounts.show', $account->id))
            ->assertStatus(Response::HTTP_OK);

        $response->assertExactJsonStructure([
            'data' => [
                'id',
                'balance' => [
                    'amount',
                    'currency',
                    'formatted',
                ],
                'created_at',
                'user' => [
                    'id',
                    'name',
                    'created_at',
                ]
            ]
        ]);


    }

    #[Test]
    public function it_returns_403_when_the_account_belongs_to_other_user(): void
    {
        $otherUserAccount = Account::factory()->for(User::factory())->create();

        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson(route('accounts.show', $otherUserAccount->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJsonMissingExact(['data']);
    }

    #[Test]
    public function it_returns_401_when_showing_an_account_without_authentication(): void
    {
        $otherUserAccount = Account::factory()->for(User::factory())->create();

        $response = $this->getJson(route('accounts.show', $otherUserAccount->id))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonMissingExact(['data']);
    }

    #[Test]
    public function it_returns_401_when_there_is_no_authenticated_user_for_the_index(): void
    {
        Account::factory(3)->create();

        $response = $this->getJson(route('accounts.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonMissingExact(['data']);
    }

    #[Test]
    public function it_returns_empty_when_the_user_does_not_have_any_account(): void
    {
        Account::factory(3)->for(User::factory())->create();

        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson(route('accounts.index'))
            ->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_returns_the_accounts_that_belongs_to_the_user(): void
    {
        Account::factory(3)->for(User::factory())->create();

        $user = User::factory()->hasAccounts(2)->create();
        Passport::actingAs($user);

        $response = $this->getJson(route('accounts.index'))
            ->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(2, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'balance' => [
                        'amount',
                        'currency',
                        'formatted',
                    ],
                    'created_at',
                ]
            ]
        ]);
    }
}
