<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    use AuthorizesRequests;

    protected function sendResponse(mixed $data, string $message, int $code): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
