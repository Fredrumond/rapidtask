<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\TarefaComentarioException;
use App\Http\Requests\StoreTarefaComentarioRequest;
use App\Http\Requests\UpdateTarefaComentarioRequest;
use App\Models\Conta;
use App\Models\Tarefa;
use App\Models\TarefaComentario;
use App\Services\TarefaComentarioService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class TarefaComentarioController extends ApiController
{
    public function __construct(
        private readonly TarefaComentarioService $tarefaComentarioService,
    ) {}

    #[OA\Get(
        path: '/tarefas/{tarefa_id}/comentarios',
        summary: 'Listar comentários de uma tarefa',
        security: [['sanctum' => []]],
        tags: ['Comentários de Tarefa'],
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
                description: 'Lista de comentários',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/TarefaComentario'),
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
        ],
    )]
    public function index(int $tarefa_id): JsonResponse
    {
        try {
            $this->authorize('viewAny', TarefaComentario::class);

            $tarefa = Tarefa::query()->find($tarefa_id);

            if ($tarefa === null) {
                throw TarefaComentarioException::tarefaNotFound();
            }

            $this->authorize('view', $tarefa);

            $result = $this->tarefaComentarioService->list($tarefa_id);

            return $this->sendResponse(
                $result,
                'Comentários listados com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
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
        path: '/tarefas/{tarefa_id}/comentarios',
        summary: 'Criar comentário em uma tarefa',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TarefaComentarioRequest'),
        ),
        tags: ['Comentários de Tarefa'],
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
                response: 201,
                description: 'Comentário criado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TarefaComentario'),
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
    public function store(StoreTarefaComentarioRequest $request, int $tarefa_id): JsonResponse
    {
        try {
            $tarefa = Tarefa::query()->find($tarefa_id);

            if ($tarefa === null) {
                throw TarefaComentarioException::tarefaNotFound();
            }

            $this->authorize('view', $tarefa);

            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->tarefaComentarioService->create(
                (int) $conta->usuario_id,
                $tarefa_id,
                $request->comentarioAttributes(),
                (int) $conta->id,
            );

            return $this->sendResponse(
                $result,
                'Comentário criado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
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

    #[OA\Put(
        path: '/tarefas/{tarefa_id}/comentarios/{comentario_id}',
        summary: 'Atualizar comentário (somente autor)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TarefaComentarioRequest'),
        ),
        tags: ['Comentários de Tarefa'],
        parameters: [
            new OA\Parameter(
                name: 'tarefa_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'comentario_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comentário atualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TarefaComentario'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / não é o autor'),
            new OA\Response(response: 404, description: 'Comentário ou tarefa não encontrados'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function update(UpdateTarefaComentarioRequest $request, int $tarefa_id, int $comentario_id): JsonResponse
    {
        try {
            $comentario = TarefaComentario::query()
                ->where('tarefa_id', $tarefa_id)
                ->find($comentario_id);

            if ($comentario === null) {
                throw TarefaComentarioException::notFound();
            }

            $this->authorize('update', $comentario);

            $result = $this->tarefaComentarioService->update(
                $tarefa_id,
                $comentario_id,
                $request->comentarioAttributes(),
            );

            return $this->sendResponse(
                $result,
                'Comentário atualizado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
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

    #[OA\Delete(
        path: '/tarefas/{tarefa_id}/comentarios/{comentario_id}',
        summary: 'Excluir comentário (soft delete, somente autor)',
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
        tags: ['Comentários de Tarefa'],
        parameters: [
            new OA\Parameter(
                name: 'tarefa_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'comentario_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comentário excluído',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / não é o autor'),
            new OA\Response(response: 404, description: 'Comentário ou tarefa não encontrados'),
        ],
    )]
    public function destroy(Request $request, int $tarefa_id, int $comentario_id): JsonResponse
    {
        try {
            $comentario = TarefaComentario::query()
                ->where('tarefa_id', $tarefa_id)
                ->find($comentario_id);

            if ($comentario === null) {
                throw TarefaComentarioException::notFound();
            }

            $this->authorize('delete', $comentario);

            $this->tarefaComentarioService->delete($tarefa_id, $comentario_id);

            return $this->sendResponse(
                [],
                'Comentário excluído com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (TarefaComentarioException $exception) {
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
