<?php

namespace App\DTO\Tarefa;

use JsonSerializable;

class NestedUsuarioDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
