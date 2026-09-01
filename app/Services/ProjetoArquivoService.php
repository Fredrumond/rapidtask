<?php

namespace App\Services;

use App\Domain\ProjetoArquivoDomain;
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
}
