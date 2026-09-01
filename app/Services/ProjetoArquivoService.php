<?php

namespace App\Services;

use App\Domain\ProjetoArquivoDomain;
use App\DTO\ProjetoArquivo\ProjetoArquivoResponseDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use App\Exceptions\ProjetoArquivoDomainException;
use App\Exceptions\ProjetoArquivoException;
use App\Models\ProjetoArquivo;
use App\Repositories\ProjetoArquivoEloquentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProjetoArquivoService
{
    public function __construct(
        private readonly ProjetoArquivoEloquentRepository $repository,
    ) {}

    /**
     * @return Collection<int, ProjetoArquivo>
     */
    public function list(int $projetoId): Collection
    {
        $this->assertProjetoExists($projetoId);

        return $this->repository->listByProjeto($projetoId);
    }

    /**
     * @return list<ProjetoArquivoResponseDTO>
     */
    public function listResponses(int $projetoId): array
    {
        $items = [];

        foreach ($this->list($projetoId) as $arquivo) {
            $items[] = $this->convertToDTO($this->convertRecordToDomain($arquivo));
        }

        Log::info('api_projeto_arquivo_listed', [
            'conta_id' => auth()->id(),
            'time_id' => current_time_id(),
            'projeto_id' => $projetoId,
            'action' => 'list',
            'count' => count($items),
        ]);

        return $items;
    }

    public function present(ProjetoArquivo $arquivo): ProjetoArquivoResponseDTO
    {
        return $this->convertToDTO($this->convertRecordToDomain($arquivo));
    }

    /**
     * @param  array{nome: string, descricao: string, arquivo: UploadedFile}  $data
     */
    public function create(int $usuarioId, int $projetoId, array $data, ?int $contaId = null): ProjetoArquivo
    {
        $this->assertProjetoExists($projetoId);

        try {
            $result = DB::transaction(function () use ($usuarioId, $projetoId, $data): ProjetoArquivo {
                $uploaded = $data['arquivo'];

                if (! $uploaded instanceof UploadedFile) {
                    throw ProjetoArquivoDomainException::srcObrigatorio();
                }

                $src = $uploaded->store('projetos/'.$projetoId, 'local');

                if ($src === false) {
                    throw ProjetoArquivoException::createFailed();
                }

                $domain = ProjetoArquivoDomain::criar(
                    projetoId: $projetoId,
                    usuarioId: $usuarioId,
                    nome: (string) $data['nome'],
                    descricao: (string) $data['descricao'],
                    src: $src,
                );

                return $this->repository->create($domain->toPersistenceArray());
            });

            Log::info('projeto_arquivo_uploaded', [
                'conta_id' => $contaId ?? current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'arquivo_id' => $result->id,
                'action' => 'create',
            ]);

            return $result;
        } catch (ProjetoArquivoDomainException $exception) {
            throw $exception;
        } catch (ProjetoArquivoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('projeto_arquivo_create_failed', [
                'conta_id' => $contaId ?? current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'action' => 'create',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoArquivoException::createFailed();
        }
    }

    public function delete(int $projetoId, int $arquivoId): void
    {
        $this->assertProjetoExists($projetoId);

        try {
            DB::transaction(function () use ($projetoId, $arquivoId): void {
                $arquivo = $this->repository->findForProjeto($projetoId, $arquivoId);

                if ($arquivo === null) {
                    throw ProjetoArquivoException::notFound();
                }

                $this->repository->delete($arquivo);
            });

            Log::info('projeto_arquivo_deleted', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'arquivo_id' => $arquivoId,
                'action' => 'delete',
            ]);
        } catch (ProjetoArquivoException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('projeto_arquivo_delete_failed', [
                'conta_id' => current_conta_id(),
                'time_id' => current_time_id(),
                'projeto_id' => $projetoId,
                'arquivo_id' => $arquivoId,
                'action' => 'delete',
                'error' => $exception->getMessage(),
            ]);

            throw ProjetoArquivoException::deleteFailed();
        }
    }

    private function assertProjetoExists(int $projetoId): void
    {
        if (! $this->repository->projetoExists($projetoId)) {
            throw ProjetoArquivoException::projetoNotFound();
        }
    }

    private function convertRecordToDomain(ProjetoArquivo $arquivo): ProjetoArquivoDomain
    {
        return ProjetoArquivoDomain::reconstituir(
            id: (int) $arquivo->id,
            projetoId: (int) $arquivo->projeto_id,
            usuarioId: (int) $arquivo->usuario_id,
            nome: $arquivo->nome,
            descricao: $arquivo->descricao,
            src: $arquivo->src,
            usuarioLookup: $arquivo->usuario !== null
                ? ['id' => $arquivo->usuario->id, 'name' => $arquivo->usuario->name]
                : null,
            createdAt: $arquivo->created_at?->toIso8601String(),
            updatedAt: $arquivo->updated_at?->toIso8601String(),
        );
    }

    private function convertToDTO(ProjetoArquivoDomain $domain): ProjetoArquivoResponseDTO
    {
        $usuario = $domain->getUsuario();

        return new ProjetoArquivoResponseDTO(
            id: (int) $domain->getId(),
            projetoId: $domain->getProjetoId(),
            nome: $domain->getNome(),
            descricao: $domain->getDescricao(),
            usuario: $usuario !== null ? new NestedUsuarioDTO($usuario['id'], $usuario['name']) : null,
            createdAt: $domain->getCreatedAt(),
            updatedAt: $domain->getUpdatedAt(),
        );
    }
}
