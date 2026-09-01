<?php

namespace App\Domain;

use App\Exceptions\ProjetoArquivoDomainException;

final class ProjetoArquivoDomain
{
    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    private function __construct(
        private ?int $id,
        private int $projetoId,
        private int $usuarioId,
        private string $nome,
        private string $descricao,
        private string $src,
        private ?array $usuarioLookup = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        int $projetoId,
        int $usuarioId,
        string $nome,
        string $descricao,
        string $src,
    ): self {
        return new self(
            id: null,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            nome: trim($nome),
            descricao: trim($descricao),
            src: trim($src),
        );
    }

    /**
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    public static function reconstituir(
        int $id,
        int $projetoId,
        int $usuarioId,
        string $nome,
        string $descricao,
        string $src,
        ?array $usuarioLookup = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ): self {
        return new self(
            id: $id,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            nome: $nome,
            descricao: $descricao,
            src: $src,
            usuarioLookup: $usuarioLookup,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'projeto_id' => $this->projetoId,
            'usuario_id' => $this->usuarioId,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'src' => $this->src,
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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getSrc(): string
    {
        return $this->src;
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
        if (trim($this->nome) === '') {
            throw ProjetoArquivoDomainException::nomeObrigatorio();
        }

        if (trim($this->descricao) === '') {
            throw ProjetoArquivoDomainException::descricaoObrigatoria();
        }

        if (trim($this->src) === '') {
            throw ProjetoArquivoDomainException::srcObrigatorio();
        }
    }
}
