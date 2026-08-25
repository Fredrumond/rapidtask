<?php

namespace App\Domain;

use App\Exceptions\TimeDomainException;

final class TimeDomain
{
    private function __construct(
        private ?int $id,
        private string $nome,
        private int $contaId,
        private int $criadorId,
        private bool $atorEhAdmin,
        private bool $excluido = false,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(string $nome, int $contaId, int $criadorId): self
    {
        return new self(
            id: null,
            nome: trim($nome),
            contaId: $contaId,
            criadorId: $criadorId,
            atorEhAdmin: true,
        );
    }

    public static function reconstituir(
        int $id,
        string $nome,
        int $contaId,
        int $criadorId,
        bool $atorEhAdmin = false,
        bool $excluido = false,
    ): self {
        return new self(
            id: $id,
            nome: $nome,
            contaId: $contaId,
            criadorId: $criadorId,
            atorEhAdmin: $atorEhAdmin,
            excluido: $excluido,
        );
    }

    public function excluir(): void
    {
        if (! $this->atorEhAdmin) {
            throw TimeDomainException::apenasAdminPodeExcluir();
        }

        $this->excluido = true;
    }

    public function foiExcluido(): bool
    {
        return $this->excluido;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'nome' => $this->nome,
            'usuario_id' => $this->criadorId,
            'conta_id' => $this->contaId,
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

    public function getContaId(): int
    {
        return $this->contaId;
    }

    public function getCriadorId(): int
    {
        return $this->criadorId;
    }

    public function isAtorAdmin(): bool
    {
        return $this->atorEhAdmin;
    }

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw TimeDomainException::nomeObrigatorio();
        }

        if ($this->contaId <= 0) {
            throw TimeDomainException::contaIdInvalido();
        }

        if ($this->criadorId <= 0) {
            throw TimeDomainException::criadorIdInvalido();
        }
    }
}
