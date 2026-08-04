<?php

namespace App\Repositories;

use App\Models\Tarefa;
use Illuminate\Database\Eloquent\Collection;

class TarefaEloquentRepository
{
    private const RELATIONS = [
        'tipo',
        'situacao',
        'prioridade',
        'projeto',
        'usuario',
    ];

    /**
     * @return Collection<int, Tarefa>
     */
    public function list(): Collection
    {
        return Tarefa::query()
            ->with(self::RELATIONS)
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?Tarefa
    {
        return Tarefa::query()
            ->with(self::RELATIONS)
            ->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Tarefa
    {
        /** @var Tarefa $tarefa */
        $tarefa = Tarefa::query()->create($data);

        return $tarefa->load(self::RELATIONS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Tarefa $tarefa, array $data): Tarefa
    {
        $tarefa->update($data);

        return $tarefa->fresh(self::RELATIONS);
    }

    public function delete(Tarefa $tarefa): void
    {
        $tarefa->delete();
    }
}
