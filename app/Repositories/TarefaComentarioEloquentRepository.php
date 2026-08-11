<?php

namespace App\Repositories;

use App\Models\TarefaComentario;
use Illuminate\Database\Eloquent\Collection;

class TarefaComentarioEloquentRepository
{
    private const RELATIONS = [
        'usuario',
    ];

    /**
     * @return Collection<int, TarefaComentario>
     */
    public function listByTarefa(int $tarefaId): Collection
    {
        return TarefaComentario::query()
            ->with(self::RELATIONS)
            ->where('tarefa_id', $tarefaId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?TarefaComentario
    {
        return TarefaComentario::query()
            ->with(self::RELATIONS)
            ->find($id);
    }

    public function findForTarefa(int $tarefaId, int $comentarioId): ?TarefaComentario
    {
        return TarefaComentario::query()
            ->with(self::RELATIONS)
            ->where('tarefa_id', $tarefaId)
            ->find($comentarioId);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): TarefaComentario
    {
        /** @var TarefaComentario $comentario */
        $comentario = TarefaComentario::query()->create($data);

        return $comentario->load(self::RELATIONS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TarefaComentario $comentario, array $data): TarefaComentario
    {
        $comentario->update($data);

        return $comentario->fresh(self::RELATIONS);
    }

    public function delete(TarefaComentario $comentario): void
    {
        $comentario->delete();
    }
}
