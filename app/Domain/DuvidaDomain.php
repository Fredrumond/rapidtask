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
        private string $mensagem,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        string $nome,
        string $email,
        string $telefone,
        string $mensagem,
    ): self {
        return new self(
            id: null,
            nome: trim($nome),
            email: trim($email),
            telefone: trim($telefone),
            mensagem: trim($mensagem),
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
            'mensagem' => $this->mensagem,
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

        if ($this->telefone === '') {
            throw DuvidaDomainException::telefoneObrigatorio();
        }

        if ($this->mensagem === '') {
            throw DuvidaDomainException::mensagemObrigatoria();
        }
    }
}
