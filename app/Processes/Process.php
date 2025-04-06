<?php

namespace App\Processes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

abstract class Process
{
    abstract protected function pipes(): array;

    public function run(object $payload): mixed
    {
        return DB::transaction(function () use ($payload) {

            return Pipeline::send($payload)
                ->through($this->pipes())
                ->thenReturn();

        });
    }
}
