<?php

namespace Tests\Unit\Queries;

use App\Models\Account;
use App\Models\User;
use App\Queries\UserAccountsQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserAccountsQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function for_method_returns_empty_collection_when_user_has_no_accounts(): void
    {
        User::factory()->has(Account::factory()->count(2))->create();
        $userWithoutAccounts = User::factory()->create();

        $accounts = UserAccountsQuery::for($userWithoutAccounts)->get();

        $this->assertCount(0, $accounts);
    }

    #[Test]
    public function it_returns_the_accounts_for_the_provided_user(): void
    {
        User::factory()
            ->has(Account::factory()->count(5))
            ->create();

        $user = User::factory()
            ->has(Account::factory()->count(3))
            ->create();

        $userAccounts = UserAccountsQuery::for($user)->get();

        $this->assertCount(3, $userAccounts);

        $userAccounts->each(fn ($account) => $this->assertEquals($user->id, $account->user_id));
    }

    #[Test]
    public function it_returns_the_requested_account(): void
    {
        Account::factory(5)->create();

        $account = Account::factory()->create();

        $userAccount = UserAccountsQuery::query()->find($account->id);

        $this->assertEquals($account->id, $userAccount->id);

        $this->assertTrue($userAccount->is($account));

    }
}
