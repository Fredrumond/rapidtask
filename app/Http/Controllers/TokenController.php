<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\TokenDomainException;
use App\Exceptions\TokenException;
use App\Models\Conta;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class TokenController extends ApiController
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    #[OA\Post(
        path: '/tokens',
        summary: 'Gerar token de API da conta autenticada',
        description: 'Gera um novo token. Um token ativo por conta: gerar outro revoga o anterior. O texto plano é retornado só nesta resposta.',
        security: [['sanctum' => []]],
        tags: ['Tokens'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Token gerado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Token'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ],
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->tokenService->issue($conta);

            return $this->sendResponse(
                $result,
                'Token gerado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (TokenDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (TokenException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (Throwable $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Delete(
        path: '/tokens',
        summary: 'Revogar token de API da conta autenticada',
        security: [['sanctum' => []]],
        tags: ['Tokens'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token revogado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ],
    )]
    public function destroy(Request $request): JsonResponse
    {
        try {
            /** @var Conta $conta */
            $conta = $request->user();

            $this->tokenService->revoke($conta);

            return $this->sendResponse(
                [],
                'Token revogado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (TokenDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (TokenException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
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
