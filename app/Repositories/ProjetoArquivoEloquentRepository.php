<?php

namespace App\Repositories;

use App\Models\Projeto;
use App\Models\ProjetoArquivo;
use Illuminate\Database\Eloquent\Collection;

class ProjetoArquivoEloquentRepository
{
    private const RELATIONS = [
        'usuario',
    ];

    /**
     * @return Collection<int, ProjetoArquivo>
     */
    public function listByProjeto(int $projetoId): Collection
    {
        return ProjetoArquivo::query()
            ->with(self::RELATIONS)
            ->where('projeto_id', $projetoId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function findForProjeto(int $projetoId, int $arquivoId): ?ProjetoArquivo
    {
        return ProjetoArquivo::query()
            ->with(self::RELATIONS)
            ->where('projeto_id', $projetoId)
            ->find($arquivoId);
    }

    public function projetoExists(int $projetoId): bool
    {
        return Projeto::query()->whereKey($projetoId)->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProjetoArquivo
    {
        /** @var ProjetoArquivo $arquivo */
        $arquivo = ProjetoArquivo::query()->create($data);

        return $arquivo->load(self::RELATIONS);
    }

    public function delete(ProjetoArquivo $arquivo): void
    {
        $arquivo->delete();
    }
}
