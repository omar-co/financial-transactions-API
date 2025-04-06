<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Query
{
    use ForwardsCalls;

    protected Builder $builder;
    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardDecoratedCallTo(
            $this->builder,
            $name,
            $arguments,
        );
    }
}
