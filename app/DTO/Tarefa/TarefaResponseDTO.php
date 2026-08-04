<?php

namespace App\DTO\Tarefa;

use JsonSerializable;

class TarefaResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $titulo,
        public readonly ?string $descricao,
        public readonly ?string $dtInicio,
        public readonly ?string $dtPrevista,
        public readonly ?string $dtFim,
        public readonly ?int $tempoEstimado,
        public readonly int $status,
        public readonly ?NestedLookupDTO $tipo,
        public readonly ?NestedLookupDTO $situacao,
        public readonly ?NestedLookupDTO $prioridade,
        public readonly ?NestedLookupDTO $projeto,
        public readonly ?NestedUsuarioDTO $usuario,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'dt_inicio' => $this->dtInicio,
            'dt_prevista' => $this->dtPrevista,
            'dt_fim' => $this->dtFim,
            'tempo_estimado' => $this->tempoEstimado,
            'status' => $this->status,
            'tipo' => $this->tipo,
            'situacao' => $this->situacao,
            'prioridade' => $this->prioridade,
            'projeto' => $this->projeto,
            'usuario' => $this->usuario,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
