<?php

namespace App\Actions\Auth;

use App\Actions\Action;
use App\Dtos\Auth\RegisteredUserDto;
use App\Dtos\Auth\RegisterUserDto;
use App\Models\User;

class RegisterAction implements Action
{
    public function execute(RegisterUserDto $userDto): RegisteredUserDto
    {
        $user = User::create($userDto->toArray());
        $token = $user->createToken('authToken')->accessToken;

        return new RegisteredUserDto(
            $user->id,
            $user->name,
            $user->email,
            $token,
        );
    }
}
