<?php

namespace App\Repositories;

use App\Models\Projeto;
use Illuminate\Database\Eloquent\Collection;

class ProjetoEloquentRepository
{
    private const RELATIONS = [
        'cliente',
        'time',
        'usuario',
    ];

    /**
     * @return Collection<int, Projeto>
     */
    public function list(): Collection
    {
        return Projeto::query()
            ->with(self::RELATIONS)
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?Projeto
    {
        return Projeto::query()
            ->with(self::RELATIONS)
            ->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Projeto
    {
        /** @var Projeto $projeto */
        $projeto = Projeto::query()->create($data);

        return $projeto->load(self::RELATIONS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Projeto $projeto, array $data): Projeto
    {
        $projeto->update($data);

        return $projeto->fresh(self::RELATIONS);
    }

    public function delete(Projeto $projeto): void
    {
        $projeto->delete();
    }
}
