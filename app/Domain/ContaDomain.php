<?php

namespace App\Domain;

use App\Exceptions\ContaDomainException;

final class ContaDomain
{
    private function __construct(
        private ?int $id,
        private string $nome,
        private int $usuarioId,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(string $nome, int $usuarioId): self
    {
        return new self(
            id: null,
            nome: trim($nome),
            usuarioId: $usuarioId,
        );
    }

    public static function reconstituir(int $id, string $nome, int $usuarioId): self
    {
        return new self(
            id: $id,
            nome: $nome,
            usuarioId: $usuarioId,
        );
    }

    public function renomear(string $nome, int $atorId): void
    {
        if ($atorId !== $this->usuarioId) {
            throw ContaDomainException::apenasOwnerPodeRenomear();
        }

        $this->nome = trim($nome);
        $this->assertInvariantes();
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'nome' => $this->nome,
            'usuario_id' => $this->usuarioId,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw ContaDomainException::nomeObrigatorio();
        }

        if ($this->usuarioId <= 0) {
            throw ContaDomainException::usuarioIdInvalido();
        }
    }
}
