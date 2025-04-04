<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Account */
class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user') ?? $this->user),
        ];
    }
}
