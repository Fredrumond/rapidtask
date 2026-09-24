<?php

namespace App\Repositories;

use App\Models\Duvida;

class DuvidaEloquentRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Duvida
    {
        /** @var Duvida $duvida */
        $duvida = Duvida::query()->create($data);

        return $duvida;
    }
}
