<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\RegisterAction;
use App\Dtos\Auth\RegisterUserDto;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\RegisteredUserResource;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request, RegisterAction $register)
    {
        try {
            $registeredUser = $register->execute(RegisterUserDto::fromRequest($request));

            return RegisteredUserResource::make($registeredUser)
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
