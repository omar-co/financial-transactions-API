<?php

namespace App\Dtos\Auth;

use App\Http\Requests\Api\Auth\RegisterRequest;

readonly class RegisterUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->validated('name'),
            $request->validated('email'),
            bcrypt($request->validated('password')),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
