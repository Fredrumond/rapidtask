<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\ProjetoDomainException;
use App\Exceptions\ProjetoException;
use App\Http\Requests\StoreProjetoRequest;
use App\Http\Requests\UpdateProjetoRequest;
use App\Models\Conta;
use App\Models\Projeto;
use App\Services\ProjetoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class ProjetoController extends ApiController
{
    public function __construct(
        private readonly ProjetoService $projetoService,
    ) {}

    #[OA\Get(
        path: '/projetos',
        summary: 'Listar projetos do time',
        security: [['sanctum' => []]],
        tags: ['Projetos'],
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
                description: 'Lista de projetos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Projeto'),
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
            $this->authorize('viewAny', Projeto::class);

            $result = $this->projetoService->list();

            return $this->sendResponse(
                $result,
                'Projetos listados com sucesso.',
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
        path: '/projetos/{projeto_id}',
        summary: 'Detalhe de um projeto',
        security: [['sanctum' => []]],
        tags: ['Projetos'],
        parameters: [
            new OA\Parameter(
                name: 'time_id',
                description: 'ID do time operacional',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Projeto encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Projeto'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Projeto não encontrado'),
        ],
    )]
    public function show(int $projeto_id): JsonResponse
    {
        try {
            $projeto = $this->projetoService->findModel($projeto_id);
            $this->authorize('view', $projeto);

            $result = $this->projetoService->present($projeto);

            return $this->sendResponse(
                $result,
                'Projeto encontrado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoException $exception) {
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
        path: '/projetos',
        summary: 'Criar projeto',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProjetoRequest'),
        ),
        tags: ['Projetos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Projeto criado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Projeto'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function store(StoreProjetoRequest $request): JsonResponse
    {
        try {
            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->projetoService->create(
                (int) $conta->usuario_id,
                $request->projetoAttributes(),
                (int) $conta->id,
            );

            return $this->sendResponse(
                $result,
                'Projeto criado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoDomainException $exception) {
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
        path: '/projetos/{projeto_id}',
        summary: 'Atualizar projeto (completo)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProjetoRequest'),
        ),
        tags: ['Projetos'],
        parameters: [
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Projeto atualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Projeto'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Projeto não encontrado'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function update(UpdateProjetoRequest $request, int $projeto_id): JsonResponse
    {
        try {
            $projeto = $this->projetoService->findModel($projeto_id);
            $this->authorize('update', $projeto);

            $result = $this->projetoService->update($projeto_id, $request->projetoAttributes());

            return $this->sendResponse(
                $result,
                'Projeto atualizado com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::NOT_FOUND->value,
            );
        } catch (ProjetoDomainException $exception) {
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
        path: '/projetos/{projeto_id}',
        summary: 'Excluir projeto (soft delete)',
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
        tags: ['Projetos'],
        parameters: [
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Projeto excluído',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Projeto não encontrado'),
        ],
    )]
    public function destroy(Request $request, int $projeto_id): JsonResponse
    {
        try {
            $projeto = $this->projetoService->findModel($projeto_id);
            $this->authorize('delete', $projeto);

            $this->projetoService->delete($projeto_id);

            return $this->sendResponse(
                [],
                'Projeto excluído com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoException $exception) {
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
