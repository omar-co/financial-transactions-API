<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\Account\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|money|numeric|min:1',
            'type' => [
                'required',
                Rule::enum(TransactionType::class),
            ],
        ];
    }
}
