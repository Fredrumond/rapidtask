<?php

namespace App\Domain;

use App\Exceptions\TarefaComentarioDomainException;

final class TarefaComentarioDomain
{
    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    private function __construct(
        private ?int $id,
        private int $tarefaId,
        private int $usuarioId,
        private string $comentario,
        private ?array $usuarioLookup = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(int $tarefaId, int $usuarioId, string $comentario): self
    {
        return new self(
            id: null,
            tarefaId: $tarefaId,
            usuarioId: $usuarioId,
            comentario: trim($comentario),
        );
    }

    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    public static function reconstituir(
        int $id,
        int $tarefaId,
        int $usuarioId,
        string $comentario,
        ?array $usuarioLookup = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ): self {
        return new self(
            id: $id,
            tarefaId: $tarefaId,
            usuarioId: $usuarioId,
            comentario: $comentario,
            usuarioLookup: $usuarioLookup,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function editarTexto(string $comentario): void
    {
        $this->comentario = trim($comentario);
        $this->assertInvariantes();
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'tarefa_id' => $this->tarefaId,
            'usuario_id' => $this->usuarioId,
            'comentario' => $this->comentario,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarefaId(): int
    {
        return $this->tarefaId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
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
        return $this->usuarioLookup;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    private function assertInvariantes(): void
    {
        if (trim($this->comentario) === '') {
            throw TarefaComentarioDomainException::comentarioObrigatorio();
        }
    }
}
