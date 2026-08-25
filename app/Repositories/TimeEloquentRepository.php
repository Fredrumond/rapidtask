<?php

namespace App\Repositories;

use App\Models\Time;

class TimeEloquentRepository
{
    public function find(int $id): ?Time
    {
        return Time::query()->find($id);
    }

    public function exists(int $id): bool
    {
        return Time::query()->whereKey($id)->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Time
    {
        /** @var Time $time */
        $time = Time::query()->create($data);

        return $time;
    }

    public function addMembro(Time $time, int $usuarioId, int $nivelId): void
    {
        $time->membros()->create([
            'usuario_id' => $usuarioId,
            'nivel_id' => $nivelId,
        ]);
    }

    public function delete(Time $time): void
    {
        $time->delete();
    }
}
