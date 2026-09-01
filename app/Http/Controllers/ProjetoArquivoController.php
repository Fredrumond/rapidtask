<?php

namespace App\Http\Controllers;

use App\Enums\HttpCode;
use App\Exceptions\ProjetoArquivoDomainException;
use App\Exceptions\ProjetoArquivoException;
use App\Http\Requests\StoreProjetoArquivoRequest;
use App\Models\Conta;
use App\Models\Projeto;
use App\Models\ProjetoArquivo;
use App\Services\ProjetoArquivoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Throwable;

class ProjetoArquivoController extends ApiController
{
    public function __construct(
        private readonly ProjetoArquivoService $projetoArquivoService,
    ) {}

    #[OA\Get(
        path: '/projetos/{projeto_id}/arquivos',
        summary: 'Listar arquivos de um projeto',
        security: [['sanctum' => []]],
        tags: ['Arquivos de Projeto'],
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
                description: 'Lista de arquivos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProjetoArquivo'),
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
            $this->authorize('viewAny', ProjetoArquivo::class);

            $projeto = Projeto::query()->find($projeto_id);

            if ($projeto === null) {
                throw ProjetoArquivoException::projetoNotFound();
            }

            $this->authorize('view', $projeto);

            $result = $this->projetoArquivoService->listResponses($projeto_id);

            return $this->sendResponse(
                $result,
                'Arquivos listados com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoArquivoException $exception) {
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
        path: '/projetos/{projeto_id}/arquivos',
        summary: 'Enviar arquivo em um projeto',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/ProjetoArquivoRequest'),
            ),
        ),
        tags: ['Arquivos de Projeto'],
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
                description: 'Arquivo enviado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProjetoArquivo'),
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
    public function store(StoreProjetoArquivoRequest $request, int $projeto_id): JsonResponse
    {
        try {
            $projeto = Projeto::query()->find($projeto_id);

            if ($projeto === null) {
                throw ProjetoArquivoException::projetoNotFound();
            }

            $this->authorize('view', $projeto);

            /** @var Conta $conta */
            $conta = $request->user();

            $arquivo = $this->projetoArquivoService->create(
                (int) $conta->usuario_id,
                $projeto_id,
                $request->arquivoAttributes(),
                (int) $conta->id,
            );

            return $this->sendResponse(
                $this->projetoArquivoService->present($arquivo),
                'Arquivo enviado com sucesso.',
                HttpCode::CREATED->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoArquivoDomainException $exception) {
            return $this->sendResponse(
                [],
                $exception->getMessage(),
                HttpCode::BAD_REQUEST->value,
            );
        } catch (ProjetoArquivoException $exception) {
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
        path: '/projetos/{projeto_id}/arquivos/{arquivo_id}',
        summary: 'Excluir arquivo (soft delete, somente dono)',
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
        tags: ['Arquivos de Projeto'],
        parameters: [
            new OA\Parameter(
                name: 'projeto_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
            new OA\Parameter(
                name: 'arquivo_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Arquivo excluído',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão / não é o dono'),
            new OA\Response(response: 404, description: 'Arquivo ou projeto não encontrados'),
        ],
    )]
    public function destroy(Request $request, int $projeto_id, int $arquivo_id): JsonResponse
    {
        try {
            $arquivo = ProjetoArquivo::query()
                ->where('projeto_id', $projeto_id)
                ->find($arquivo_id);

            if ($arquivo === null) {
                throw ProjetoArquivoException::notFound();
            }

            $this->authorize('delete', $arquivo);

            $this->projetoArquivoService->delete($projeto_id, $arquivo_id);

            return $this->sendResponse(
                [],
                'Arquivo excluído com sucesso.',
                HttpCode::OK->value,
            );
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (ProjetoArquivoException $exception) {
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
