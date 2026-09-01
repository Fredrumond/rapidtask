<?php

namespace App\Domain;

use App\Exceptions\ProjetoAnotacaoDomainException;

final class ProjetoAnotacaoDomain
{
    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    private function __construct(
        private ?int $id,
        private int $projetoId,
        private int $usuarioId,
        private string $anotacao,
        private ?array $usuarioLookup = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(int $projetoId, int $usuarioId, string $anotacao): self
    {
        return new self(
            id: null,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            anotacao: trim($anotacao),
        );
    }

    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    public static function reconstituir(
        int $id,
        int $projetoId,
        int $usuarioId,
        string $anotacao,
        ?array $usuarioLookup = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ): self {
        return new self(
            id: $id,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            anotacao: $anotacao,
            usuarioLookup: $usuarioLookup,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function editarTexto(string $anotacao): void
    {
        $this->anotacao = trim($anotacao);
        $this->assertInvariantes();
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'projeto_id' => $this->projetoId,
            'usuario_id' => $this->usuarioId,
            'anotacao' => $this->anotacao,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjetoId(): int
    {
        return $this->projetoId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getAnotacao(): string
    {
        return $this->anotacao;
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
        if (trim($this->anotacao) === '') {
            throw ProjetoAnotacaoDomainException::anotacaoObrigatoria();
        }
    }
}
