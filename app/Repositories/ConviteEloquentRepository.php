<?php

namespace App\Repositories;

use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\TimeMembroConvite;

class ConviteEloquentRepository
{
    public function find(int $id): ?TimeMembroConvite
    {
        return TimeMembroConvite::query()->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): TimeMembroConvite
    {
        /** @var TimeMembroConvite $convite */
        $convite = TimeMembroConvite::query()->create($data);

        return $convite;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TimeMembroConvite $convite, array $data): TimeMembroConvite
    {
        $convite->update($data);

        return $convite->fresh();
    }

    public function timeExists(int $timeId): bool
    {
        return Time::withoutGlobalScopes()->whereKey($timeId)->exists();
    }

    public function findTimeContaId(int $timeId): ?int
    {
        $contaId = Time::withoutGlobalScopes()
            ->whereKey($timeId)
            ->value('conta_id');

        return $contaId !== null ? (int) $contaId : null;
    }

    public function addMembroIfMissing(int $timeId, int $usuarioId, int $nivelId): void
    {
        TimeMembro::query()->firstOrCreate(
            [
                'time_id' => $timeId,
                'usuario_id' => $usuarioId,
            ],
            [
                'nivel_id' => $nivelId,
            ]
        );
    }
}
