<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\ClienteDomainException;
use App\Exceptions\ClienteException;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Conta;
use App\Services\ClienteApiService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Throwable;

class ClienteController extends ApiController
{
    public function __construct(
        private readonly ClienteApiService $clienteApiService,
    ) {}

    #[OA\Get(
        path: '/clientes',
        summary: 'Listar clientes do time',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(
                name: 'time_id',
                description: 'ID do time operacional',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de clientes',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Cliente'),
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'time_id ausente ou inválido'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Time não pertence à conta autenticada'),
        ],
    )]
    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', Cliente::class);

            $result = $this->clienteApiService->list($this->currentTimeId());

            return $this->sendResponse(
                $result,
                'Clientes listados com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            $this->logUnexpectedError('index', $exception);

            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Get(
        path: '/clientes/{cliente_id}',
        summary: 'Detalhe de um cliente',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(
                name: 'time_id',
                description: 'ID do time operacional',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'cliente_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Cliente não encontrado'),
        ],
    )]
    public function show(int $cliente_id): JsonResponse
    {
        try {
            $cliente = $this->resolveCliente($cliente_id);
            $this->authorize('view', $cliente);

            $result = $this->clienteApiService->find($cliente_id, $this->currentTimeId());

            return $this->sendResponse(
                $result,
                'Cliente encontrado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (Throwable $exception) {
            $this->logUnexpectedError('show', $exception, $cliente_id);

            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Post(
        path: '/clientes',
        summary: 'Criar cliente',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ClienteRequest'),
        ),
        tags: ['Clientes'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Cliente criado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function store(StoreClienteRequest $request): JsonResponse
    {
        try {
            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->clienteApiService->create([
                ...$request->validated(),
                'time_id' => $this->currentTimeId(),
                'usuario_id' => (int) $conta->usuario_id,
                'conta_id' => (int) $conta->id,
            ]);

            return $this->sendResponse(
                $result,
                'Cliente criado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ClienteDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (ClienteException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (Throwable $exception) {
            $this->logUnexpectedError('store', $exception);

            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Put(
        path: '/clientes/{cliente_id}',
        summary: 'Atualizar cliente (completo)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ClienteRequest'),
        ),
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(
                name: 'cliente_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente atualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Cliente'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Cliente não encontrado'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function update(UpdateClienteRequest $request, int $cliente_id): JsonResponse
    {
        try {
            $cliente = $this->resolveCliente($cliente_id);
            $this->authorize('update', $cliente);

            $result = $this->clienteApiService->update(
                $cliente_id,
                $request->clienteAttributes(),
                $this->currentTimeId(),
            );

            return $this->sendResponse(
                $result,
                'Cliente atualizado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (ClienteDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (Throwable $exception) {
            $this->logUnexpectedError('update', $exception, $cliente_id);

            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Delete(
        path: '/clientes/{cliente_id}',
        summary: 'Excluir cliente (soft delete)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['time_id'],
                properties: [
                    new OA\Property(property: 'time_id', type: 'integer'),
                ],
            ),
        ),
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(
                name: 'cliente_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cliente excluído',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Cliente não encontrado'),
        ],
    )]
    public function destroy(Request $request, int $cliente_id): JsonResponse
    {
        try {
            $cliente = $this->resolveCliente($cliente_id);
            $this->authorize('delete', $cliente);

            $this->clienteApiService->delete($cliente_id, $this->currentTimeId());

            return $this->sendResponse(
                [],
                'Cliente excluído com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ClienteException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (Throwable $exception) {
            $this->logUnexpectedError('destroy', $exception, $cliente_id);

            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    private function currentTimeId(): int
    {
        return (int) current_time_id();
    }

    private function resolveCliente(int $clienteId): Cliente
    {
        $cliente = Cliente::query()->find($clienteId);

        if ($cliente === null) {
            throw ClienteException::notFound();
        }

        return $cliente;
    }

    private function logUnexpectedError(string $action, Throwable $exception, ?int $clienteId = null): void
    {
        Log::error('api_cliente_unexpected_error', array_filter([
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'cliente_id' => $clienteId,
            'action' => $action,
            'error' => $exception->getMessage(),
        ], fn (mixed $value): bool => $value !== null));
    }
}
