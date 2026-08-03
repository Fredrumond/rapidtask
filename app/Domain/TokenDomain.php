<?php

namespace App\Domain;

class TokenDomain
{
    public const TOKEN_NAME = 'api';

    public function __construct(
        private readonly bool $active,
        private readonly ?string $plainTextToken = null,
        private readonly ?string $createdAt = null,
    ) {}

    public function isActive(): bool
    {
        return $this->active;
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

    public function withPlainTextToken(string $plainTextToken): self
    {
        return new self(
            active: true,
            plainTextToken: $plainTextToken,
            createdAt: $this->createdAt,
        );
    }

    public static function inactive(): self
    {
        return new self(active: false);
    }

    public static function active(?string $createdAt = null): self
    {
        return new self(
            active: true,
            plainTextToken: null,
            createdAt: $createdAt,
        );
    }
}
