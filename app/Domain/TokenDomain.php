<?php

namespace App\Domain;

use App\Enums\TokenStatus;
use App\Exceptions\TokenDomainException;

final class TokenDomain
{
    public const TOKEN_NAME = 'api';

    private function __construct(
        private TokenStatus $status,
        private ?string $plainTextToken = null,
        private ?string $createdAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(): self
    {
        return new self(status: TokenStatus::Ativo);
    }

    public static function reconstituir(
        TokenStatus $status,
        ?string $createdAt = null,
        ?string $plainTextToken = null,
    ): self {
        return new self(
            status: $status,
            plainTextToken: $plainTextToken,
            createdAt: $createdAt,
        );
    }

    public static function inactive(): self
    {
        return self::reconstituir(TokenStatus::Inativo);
    }

    public static function active(?string $createdAt = null): self
    {
        return self::reconstituir(TokenStatus::Ativo, $createdAt);
    }

    public function anexarTextoPlano(string $plainTextToken): void
    {
        if (! $this->status->podeAnexarTextoPlano()) {
            throw TokenDomainException::textoPlanoEmInativo();
        }

        $this->plainTextToken = trim($plainTextToken);
        $this->assertInvariantes();
    }

    public function revogar(): void
    {
        if (! $this->status->podeRevogar()) {
            throw TokenDomainException::jaRevogado();
        }

        $this->status = TokenStatus::Inativo;
        $this->plainTextToken = null;
    }

    public function isActive(): bool
    {
        return $this->status === TokenStatus::Ativo;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'name' => self::TOKEN_NAME,
        ];
    }

    public function getPlainTextToken(): ?string
    {
        return $this->plainTextToken;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getTokenName(): string
    {
        return self::TOKEN_NAME;
    }

    public function getStatusEnum(): TokenStatus
    {
        return $this->status;
    }

    private function assertInvariantes(): void
    {
        if ($this->status === TokenStatus::Inativo && $this->plainTextToken !== null) {
            throw TokenDomainException::textoPlanoEmInativo();
        }

        if ($this->plainTextToken !== null && trim($this->plainTextToken) === '') {
            throw TokenDomainException::textoPlanoObrigatorio();
        }
    }
}
