<?php

namespace App\Http\Controllers\Api;

use App\Actions\Account\CreateAccountAction;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Queries\UserAccountsQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AccountController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Account::class);

        $userAccounts = UserAccountsQuery::for($request->user())->get();

        return AccountResource::collection($userAccounts);
    }

    public function store(Request $request, CreateAccountAction $createAccount): AccountResource
    {
        Gate::authorize('create', Account::class);

        $user = $request->user();
        $account = $createAccount->execute($user);

        return new AccountResource($account);
    }

    public function show(Request $request, Account $account): AccountResource
    {
        Gate::authorize('view', $account);

        return new AccountResource($account->setRelation('user', $request->user()));
    }
}
