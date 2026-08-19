<?php

namespace App\Repositories;

use App\Models\Cliente;

class ClienteEloquentRepository
{
    public function find(int $id): ?Cliente
    {
        return Cliente::query()->find($id);
    }

    public function exists(int $id): bool
    {
        return Cliente::query()->whereKey($id)->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = Cliente::query()->create($data);

        return $cliente;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);

        return $cliente->fresh();
    }

    public function delete(Cliente $cliente): void
    {
        $cliente->delete();
    }
}
