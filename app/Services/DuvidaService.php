<?php

namespace App\Services;

use App\Domain\DuvidaDomain;
use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
use App\Models\Duvida;
use App\Repositories\DuvidaEloquentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DuvidaService
{
    public function __construct(
        private readonly DuvidaEloquentRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function registrar(array $data): void
    {
        try {
            $duvida = DB::transaction(function () use ($data): Duvida {
                $domain = DuvidaDomain::criar(
                    nome: (string) ($data['nome'] ?? ''),
                    email: (string) ($data['email'] ?? ''),
                    telefone: (string) ($data['telefone'] ?? ''),
                    mensagem: (string) ($data['mensagem'] ?? ''),
                );

                return $this->repository->create($domain->toPersistenceArray());
            });

            Log::info('duvida_registrada', [
                'duvida_id' => $duvida->id,
                'action' => 'registrar',
            ]);
        } catch (DuvidaDomainException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('duvida_registrar_failed', [
                'action' => 'registrar',
                'error' => $exception->getMessage(),
            ]);

            throw DuvidaException::createFailed();
        }
    }
}
