<?php

namespace App\DTO\Projeto;

use App\DTO\Tarefa\NestedLookupDTO;
use App\DTO\Tarefa\NestedUsuarioDTO;
use JsonSerializable;

class ProjetoResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $sigla,
        public readonly ?string $descricao,
        public readonly ?string $dtInicio,
        public readonly ?string $dtPrevista,
        public readonly ?string $dtFim,
        public readonly ?NestedLookupDTO $cliente,
        public readonly ?NestedLookupDTO $time,
        public readonly ?NestedUsuarioDTO $usuario,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'descricao' => $this->descricao,
            'dt_inicio' => $this->dtInicio,
            'dt_prevista' => $this->dtPrevista,
            'dt_fim' => $this->dtFim,
            'cliente' => $this->cliente,
            'time' => $this->time,
            'usuario' => $this->usuario,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
