<?php

namespace App\Http\Controllers;

use App\Exceptions\TarefaDomainException;
use App\Enums\HttpCode;
use App\Exceptions\TarefaException;
use App\Http\Requests\StoreTarefaRequest;
use App\Http\Requests\UpdateTarefaRequest;
use App\Models\Conta;
use App\Models\Tarefa;
use App\Services\TarefaService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class TarefaController extends ApiController
{
    public function __construct(
        private readonly TarefaService $tarefaService,
    ) {}

    #[OA\Get(
        path: '/tarefas',
        summary: 'Listar tarefas do time',
        security: [['sanctum' => []]],
        tags: ['Tarefas'],
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
                description: 'Lista de tarefas',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Tarefa'),
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
            $this->authorize('viewAny', Tarefa::class);

            $result = $this->tarefaService->list();

            return $this->sendResponse(
                $result,
                'Tarefas listadas com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Get(
        path: '/tarefas/{tarefa_id}',
        summary: 'Detalhe de uma tarefa',
        security: [['sanctum' => []]],
        tags: ['Tarefas'],
        parameters: [
            new OA\Parameter(
                name: 'time_id',
                description: 'ID do time operacional',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'tarefa_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tarefa encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tarefa'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
        ],
    )]
    public function show(int $tarefa_id): JsonResponse
    {
        try {
            $tarefa = $this->tarefaService->findModel($tarefa_id);
            $this->authorize('view', $tarefa);

            $result = $this->tarefaService->present($tarefa);

            return $this->sendResponse(
                $result,
                'Tarefa encontrada com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (Throwable $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        }
    }

    #[OA\Post(
        path: '/tarefas',
        summary: 'Criar tarefa',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TarefaRequest'),
        ),
        tags: ['Tarefas'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tarefa criada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tarefa'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function store(StoreTarefaRequest $request): JsonResponse
    {
        try {
            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->tarefaService->create(
                (int) $conta->usuario_id,
                $request->tarefaAttributes(),
                (int) $conta->id,
            );

            return $this->sendResponse(
                $result,
                'Tarefa criada com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaDomainException $exception) {
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

    #[OA\Put(
        path: '/tarefas/{tarefa_id}',
        summary: 'Atualizar tarefa (completo)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TarefaRequest'),
        ),
        tags: ['Tarefas'],
        parameters: [
            new OA\Parameter(
                name: 'tarefa_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tarefa atualizada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Tarefa'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function update(UpdateTarefaRequest $request, int $tarefa_id): JsonResponse
    {
        try {
            $tarefa = $this->tarefaService->findModel($tarefa_id);
            $this->authorize('update', $tarefa);

            $result = $this->tarefaService->update($tarefa_id, $request->tarefaAttributes());

            return $this->sendResponse(
                $result,
                'Tarefa atualizada com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (TarefaDomainException $exception) {
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
        path: '/tarefas/{tarefa_id}',
        summary: 'Excluir tarefa (soft delete)',
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
        tags: ['Tarefas'],
        parameters: [
            new OA\Parameter(
                name: 'tarefa_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tarefa excluída',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
        ],
    )]
    public function destroy(Request $request, int $tarefa_id): JsonResponse
    {
        try {
            $tarefa = $this->tarefaService->findModel($tarefa_id);
            $this->authorize('delete', $tarefa);

            $this->tarefaService->delete($tarefa_id);

            return $this->sendResponse(
                [],
                'Tarefa excluída com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
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
