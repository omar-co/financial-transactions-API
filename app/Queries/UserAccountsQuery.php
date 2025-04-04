<?php

namespace App\Queries;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;

/** @mixin Builder */
class UserAccountsQuery
{
    use ForwardsCalls;
    private Builder $builder;

    public function __construct()
    {
        $this->builder = Account::query();
    }

    public static function for(User $user): self
    {
        return (new self())
            ->whereBelongsTo($user);
    }

    public static function query(): self
    {
        return new self();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardDecoratedCallTo(
            $this->builder,
            $name,
            $arguments,
        );
    }
}
