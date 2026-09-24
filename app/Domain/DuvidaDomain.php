<?php

namespace App\Domain;

use App\Exceptions\DuvidaDomainException;

final class DuvidaDomain
{
    private function __construct(
        private ?int $id,
        private string $nome,
        private string $email,
        private string $telefone,
        private string $duvida,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        string $nome,
        string $email,
        string $telefone,
        string $duvida,
    ): self {
        return new self(
            id: null,
            nome: trim($nome),
            email: strtolower(trim($email)),
            telefone: trim($telefone),
            duvida: trim($duvida),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'duvida' => $this->duvida,
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

    public function getTelefone(): string
    {
        return $this->telefone;
    }

    public function getDuvida(): string
    {
        return $this->duvida;
    }

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw DuvidaDomainException::nomeObrigatorio();
        }

        if ($this->email === '') {
            throw DuvidaDomainException::emailObrigatorio();
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw DuvidaDomainException::emailInvalido();
        }

        if ($this->telefone === '') {
            throw DuvidaDomainException::telefoneObrigatorio();
        }

        if ($this->duvida === '') {
            throw DuvidaDomainException::duvidaObrigatoria();
        }
    }
}
