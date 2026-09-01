<?php

namespace App\DTO\ProjetoAnotacao;

use App\DTO\Tarefa\NestedUsuarioDTO;
use JsonSerializable;

class ProjetoAnotacaoResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $projetoId,
        public readonly string $anotacao,
        public readonly ?NestedUsuarioDTO $usuario,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'projeto_id' => $this->projetoId,
            'anotacao' => $this->anotacao,
            'usuario' => $this->usuario,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
