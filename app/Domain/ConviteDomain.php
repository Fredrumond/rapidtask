<?php

namespace App\Domain;

use App\Exceptions\ConviteDomainException;

final class ConviteDomain
{
    public const STATUS_PENDENTE = 0;

    public const STATUS_ACEITO = 1;

    public const STATUS_RECUSADO = 2;

    private function __construct(
        private ?int $id,
        private string $nome,
        private string $email,
        private int $timeId,
        private string $token,
        private int $status,
        private ?int $contaId = null,
    ) {
        $this->assertInvariantes();
    }

    public static function emitir(
        string $nome,
        string $email,
        string $token,
        int $timeId,
        bool $emailJaPertenceAOutraConta = false,
    ): self {
        if ($emailJaPertenceAOutraConta) {
            throw ConviteDomainException::emailJaPertenceAOutraConta();
        }

        return new self(
            id: null,
            nome: trim($nome),
            email: trim($email),
            timeId: $timeId,
            token: $token,
            status: self::STATUS_PENDENTE,
        );
    }

    public static function reconstituir(
        int $id,
        string $nome,
        string $email,
        int $timeId,
        string $token,
        int $status,
        ?int $contaId = null,
    ): self {
        return new self(
            id: $id,
            nome: $nome,
            email: $email,
            timeId: $timeId,
            token: $token,
            status: $status,
            contaId: $contaId,
        );
    }

    public function aceitar(string $usuarioEmail, ?int $usuarioContaId): void
    {
        $this->assertPendente();
        $this->assertEmailCoincidente($usuarioEmail);
        $this->assertNaoPertenceAOutraConta($usuarioContaId);

        $this->status = self::STATUS_ACEITO;
    }

    public function recusar(string $usuarioEmail): void
    {
        $this->assertPendente();
        $this->assertEmailCoincidente($usuarioEmail);

        $this->status = self::STATUS_RECUSADO;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'nome' => $this->nome,
            'email' => $this->email,
            'time_id' => $this->timeId,
            'token' => $this->token,
            'status' => $this->status,
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTimeId(): int
    {
        return $this->timeId;
    }

    public function getContaId(): ?int
    {
        return $this->contaId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function foiAceito(): bool
    {
        return $this->status === self::STATUS_ACEITO;
    }

    public function foiRecusado(): bool
    {
        return $this->status === self::STATUS_RECUSADO;
    }

    private function assertPendente(): void
    {
        if ($this->status !== self::STATUS_PENDENTE) {
            throw ConviteDomainException::conviteNaoPendente();
        }
    }

    private function assertEmailCoincidente(string $usuarioEmail): void
    {
        if (strcasecmp(trim($usuarioEmail), $this->email) !== 0) {
            throw ConviteDomainException::emailNaoCoincidente();
        }
    }

    private function assertNaoPertenceAOutraConta(?int $usuarioContaId): void
    {
        if ($usuarioContaId === null || $this->contaId === null) {
            return;
        }

        if ($usuarioContaId !== $this->contaId) {
            throw ConviteDomainException::usuarioJaPertenceAOutraConta();
        }
    }

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw ConviteDomainException::nomeObrigatorio();
        }

        if ($this->email === '') {
            throw ConviteDomainException::emailObrigatorio();
        }

        if ($this->token === '') {
            throw ConviteDomainException::tokenObrigatorio();
        }

        if ($this->timeId <= 0) {
            throw ConviteDomainException::timeIdInvalido();
        }
    }
}
