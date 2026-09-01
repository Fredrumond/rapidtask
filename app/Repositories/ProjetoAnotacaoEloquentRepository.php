<?php

namespace App\Repositories;

use App\Models\ProjetoAnotacao;
use Illuminate\Database\Eloquent\Collection;

class ProjetoAnotacaoEloquentRepository
{
    private const RELATIONS = [
        'usuario',
    ];

    /**
     * @return Collection<int, ProjetoAnotacao>
     */
    public function listByProjeto(int $projetoId): Collection
    {
        return ProjetoAnotacao::query()
            ->with(self::RELATIONS)
            ->where('projeto_id', $projetoId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?ProjetoAnotacao
    {
        return ProjetoAnotacao::query()
            ->with(self::RELATIONS)
            ->find($id);
    }

    public function findForProjeto(int $projetoId, int $anotacaoId): ?ProjetoAnotacao
    {
        return ProjetoAnotacao::query()
            ->with(self::RELATIONS)
            ->where('projeto_id', $projetoId)
            ->find($anotacaoId);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProjetoAnotacao
    {
        /** @var ProjetoAnotacao $anotacao */
        $anotacao = ProjetoAnotacao::query()->create($data);

        return $anotacao->load(self::RELATIONS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProjetoAnotacao $anotacao, array $data): ProjetoAnotacao
    {
        $anotacao->update($data);

        return $anotacao->fresh(self::RELATIONS);
    }

    public function delete(ProjetoAnotacao $anotacao): void
    {
        $anotacao->delete();
    }
}
