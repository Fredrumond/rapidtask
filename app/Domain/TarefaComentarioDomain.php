<?php

namespace App\Domain;

class TarefaComentarioDomain
{
    /**
     * @param  array{id: int, name: string}|null  $usuario
     */
    public function __construct(
        private readonly ?int $id,
        private readonly int $tarefaId,
        private readonly string $comentario,
        private readonly ?array $usuario,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarefaId(): int
    {
        return $this->tarefaId;
    }

    public function getComentario(): string
    {
        return $this->comentario;
    }

    /**
     * @return array{id: int, name: string}|null
     */
    public function getUsuario(): ?array
    {
        return $this->usuario;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
