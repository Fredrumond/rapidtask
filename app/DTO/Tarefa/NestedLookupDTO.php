<?php

namespace App\DTO\Tarefa;

use JsonSerializable;

class NestedLookupDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
        ];
    }
}
