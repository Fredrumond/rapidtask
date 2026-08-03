<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TokenController extends ApiController
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $result = $this->tokenService->issue($request->user());

            return $this->sendResponse(
                $result,
                'Token gerado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (Throwable $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $this->tokenService->revoke($request->user());

            return $this->sendResponse(
                [],
                'Token revogado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (Throwable $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }
}
