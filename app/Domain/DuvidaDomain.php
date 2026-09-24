<?php

namespace App\Domain;

use App\Exceptions\DuvidaDomainException;

final class DuvidaDomain
{
    private function __construct(
        private string $nome,
        private string $email,
        private string $telefone,
        private string $duvida,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(string $nome, string $email, string $telefone, string $duvida): self
    {
        return new self(
            nome: trim($nome),
            email: trim($email),
            telefone: trim($telefone),
            duvida: trim($duvida),
        );
    }

    /**
     * @return array{nome: string, email: string, telefone: string, duvida: string}
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

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw DuvidaDomainException::nomeObrigatorio();
        }

        if ($this->email === '') {
            throw DuvidaDomainException::emailObrigatorio();
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
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
