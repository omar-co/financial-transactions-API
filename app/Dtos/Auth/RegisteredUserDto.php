<?php

namespace App\Dtos\Auth;

readonly class RegisteredUserDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $token,
    ) {
    }
}
