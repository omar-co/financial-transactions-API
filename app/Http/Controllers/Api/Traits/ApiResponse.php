<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function error(string $message, int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {

        return response()->json([
            'message' => $message,
        ], $code);
    }
}
