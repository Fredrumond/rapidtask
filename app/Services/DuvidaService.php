<?php

namespace App\Services;

use App\Domain\DuvidaDomain;
use App\Exceptions\DuvidaDomainException;
use App\Exceptions\DuvidaException;
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
     * @param  array{nome: string, email: string, telefone: string, duvida: string}  $data
     */
    public function create(array $data): void
    {
        try {
            DB::transaction(function () use ($data): void {
                $domain = DuvidaDomain::criar(
                    nome: (string) $data['nome'],
                    email: (string) $data['email'],
                    telefone: (string) $data['telefone'],
                    duvida: (string) $data['duvida'],
                );

                $this->repository->create($domain->toPersistenceArray());
            });

            Log::info('duvida_created', [
                'action' => 'create',
            ]);
        } catch (DuvidaDomainException $exception) {
            throw $exception;
        } catch (DuvidaException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('duvida_create_failed', [
                'action' => 'create',
            ]);

            throw DuvidaException::createFailed();
        }
    }
}
