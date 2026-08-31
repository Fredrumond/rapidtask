<?php

namespace App\Repositories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Collection;

class ClienteEloquentRepository
{
    private const RELATIONS = [
        'time',
        'usuario',
    ];

    /**
     * @return Collection<int, Cliente>
     */
    public function findByTime(int $timeId): Collection
    {
        return Cliente::query()
            ->with(self::RELATIONS)
            ->where('time_id', $timeId)
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?Cliente
    {
        return Cliente::query()->find($id);
    }

    public function findInTime(int $id, int $timeId): ?Cliente
    {
        return Cliente::query()
            ->with(self::RELATIONS)
            ->where('time_id', $timeId)
            ->find($id);
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

        return $cliente->load(self::RELATIONS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);

        return $cliente->fresh(self::RELATIONS);
    }

    public function delete(Cliente $cliente): void
    {
        $cliente->delete();
    }
}
