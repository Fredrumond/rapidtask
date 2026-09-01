<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\ProjetoAnotacaoDomainException;
use App\Exceptions\ProjetoAnotacaoException;
use App\Http\Requests\StoreProjetoAnotacaoRequest;
use App\Http\Requests\UpdateProjetoAnotacaoRequest;
use App\Models\Conta;
use App\Models\Projeto;
use App\Models\ProjetoAnotacao;
use App\Services\ProjetoAnotacaoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class ProjetoAnotacaoController extends ApiController
{
    public function __construct(
        private readonly ProjetoAnotacaoService $projetoAnotacaoService,
    ) {}

    #[OA\Get(
        path: '/projetos/{projeto_id}/anotacoes',
        summary: 'Listar anotações de um projeto',
        security: [['sanctum' => []]],
        tags: ['Anotações de Projeto'],
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
                description: 'Lista de anotações',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProjetoAnotacao'),
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / time inválido'),
            new OA\Response(response: 404, description: 'Projeto não encontrado'),
        ],
    )]
    public function index(int $projeto_id): JsonResponse
    {
        try {
            $this->authorize('viewAny', ProjetoAnotacao::class);

            $projeto = Projeto::query()->find($projeto_id);

            if ($projeto === null) {
                throw ProjetoAnotacaoException::projetoNotFound();
            }

            $this->authorize('view', $projeto);

            $result = $this->projetoAnotacaoService->listForApi($projeto_id);

            return $this->sendResponse(
                $result,
                'Anotações listadas com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoException $exception) {
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
        path: '/projetos/{projeto_id}/anotacoes',
        summary: 'Criar anotação em um projeto',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProjetoAnotacaoRequest'),
        ),
        tags: ['Anotações de Projeto'],
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
                response: 201,
                description: 'Anotação criada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProjetoAnotacao'),
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
    public function store(StoreProjetoAnotacaoRequest $request, int $projeto_id): JsonResponse
    {
        try {
            $projeto = Projeto::query()->find($projeto_id);

            if ($projeto === null) {
                throw ProjetoAnotacaoException::projetoNotFound();
            }

            $this->authorize('view', $projeto);

            /** @var Conta $conta */
            $conta = $request->user();

            $result = $this->projetoAnotacaoService->create(
                (int) $conta->usuario_id,
                $projeto_id,
                $request->anotacaoAttributes(),
                (int) $conta->id,
            );

            return $this->sendResponse(
                $result,
                'Anotação criada com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (ProjetoAnotacaoException $exception) {
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
        path: '/projetos/{projeto_id}/anotacoes/{anotacao_id}',
        summary: 'Atualizar anotação (somente autor)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProjetoAnotacaoRequest'),
        ),
        tags: ['Anotações de Projeto'],
        parameters: [
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'anotacao_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Anotação atualizada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProjetoAnotacao'),
                    ],
                ),
            ),
            new OA\Response(response: 400, description: 'Erro de negócio'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / não é o autor'),
            new OA\Response(response: 404, description: 'Anotação ou projeto não encontrados'),
            new OA\Response(response: 422, description: 'Validação falhou'),
        ],
    )]
    public function update(UpdateProjetoAnotacaoRequest $request, int $projeto_id, int $anotacao_id): JsonResponse
    {
        try {
            $anotacao = ProjetoAnotacao::query()
                ->where('projeto_id', $projeto_id)
                ->find($anotacao_id);

            if ($anotacao === null) {
                throw ProjetoAnotacaoException::notFound();
            }

            $this->authorize('update', $anotacao);

            $result = $this->projetoAnotacaoService->update(
                $projeto_id,
                $anotacao_id,
                $request->anotacaoAttributes(),
            );

            return $this->sendResponse(
                $result,
                'Anotação atualizada com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (ProjetoAnotacaoException $exception) {
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
        path: '/projetos/{projeto_id}/anotacoes/{anotacao_id}',
        summary: 'Excluir anotação (soft delete, somente autor)',
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
        tags: ['Anotações de Projeto'],
        parameters: [
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'anotacao_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Anotação excluída',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / não é o autor'),
            new OA\Response(response: 404, description: 'Anotação ou projeto não encontrados'),
        ],
    )]
    public function destroy(Request $request, int $projeto_id, int $anotacao_id): JsonResponse
    {
        try {
            $anotacao = ProjetoAnotacao::query()
                ->where('projeto_id', $projeto_id)
                ->find($anotacao_id);

            if ($anotacao === null) {
                throw ProjetoAnotacaoException::notFound();
            }

            $this->authorize('delete', $anotacao);

            $this->projetoAnotacaoService->delete($projeto_id, $anotacao_id);

            return $this->sendResponse(
                [],
                'Anotação excluída com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoAnotacaoException $exception) {
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
