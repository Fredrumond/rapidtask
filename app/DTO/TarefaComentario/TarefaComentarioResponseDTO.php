<?php

namespace App\DTO\TarefaComentario;

use App\DTO\Tarefa\NestedUsuarioDTO;
use JsonSerializable;

class TarefaComentarioResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $tarefaId,
        public readonly string $comentario,
        public readonly ?NestedUsuarioDTO $usuario,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'tarefa_id' => $this->tarefaId,
            'comentario' => $this->comentario,
            'usuario' => $this->usuario,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
