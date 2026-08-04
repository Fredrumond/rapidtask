<?php

namespace App\Domain;

class TarefaDomain
{
    /**
     * @param  array{id: int, nome: string}|null  $tipo
     * @param  array{id: int, nome: string}|null  $situacao
     * @param  array{id: int, nome: string}|null  $prioridade
     * @param  array{id: int, nome: string}|null  $projeto
     * @param  array{id: int, name: string}|null  $usuario
     */
    public function __construct(
        private readonly ?int $id,
        private readonly string $titulo,
        private readonly ?string $descricao,
        private readonly ?string $dtInicio,
        private readonly ?string $dtPrevista,
        private readonly ?string $dtFim,
        private readonly ?int $tempoEstimado,
        private readonly int $status,
        private readonly ?array $tipo,
        private readonly ?array $situacao,
        private readonly ?array $prioridade,
        private readonly ?array $projeto,
        private readonly ?array $usuario,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getDtInicio(): ?string
    {
        return $this->dtInicio;
    }

    public function getDtPrevista(): ?string
    {
        return $this->dtPrevista;
    }

    public function getDtFim(): ?string
    {
        return $this->dtFim;
    }

    public function getTempoEstimado(): ?int
    {
        return $this->tempoEstimado;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getTipo(): ?array
    {
        return $this->tipo;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getSituacao(): ?array
    {
        return $this->situacao;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getPrioridade(): ?array
    {
        return $this->prioridade;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getProjeto(): ?array
    {
        return $this->projeto;
    }

    /**
     * @return array{id: int, name: string}|null
     */
    public function getUsuario(): ?array
    {
        return $this->usuario;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
