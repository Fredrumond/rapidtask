<?php

namespace App\Repositories;

use App\Models\Conta;

class ContaEloquentRepository
{
    public function find(int $id): ?Conta
    {
        return Conta::query()->find($id);
    }

    public function findByUsuarioId(int $usuarioId): ?Conta
    {
        return Conta::query()->where('usuario_id', $usuarioId)->first();
    }

    public function exists(int $id): bool
    {
        return Conta::query()->whereKey($id)->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Conta
    {
        /** @var Conta $conta */
        $conta = Conta::query()->create($data);

        return $conta;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Conta $conta, array $data): Conta
    {
        $conta->update($data);

        return $conta->fresh();
    }
}
