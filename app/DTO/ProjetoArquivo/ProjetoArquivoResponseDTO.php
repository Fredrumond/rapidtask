<?php

namespace App\DTO\ProjetoArquivo;

use App\DTO\Tarefa\NestedUsuarioDTO;
use JsonSerializable;

class ProjetoArquivoResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $projetoId,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly ?NestedUsuarioDTO $usuario,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'projeto_id' => $this->projetoId,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'usuario' => $this->usuario,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
