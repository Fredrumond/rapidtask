<?php

namespace App\Domain;

use App\Exceptions\ProjetoDomainException;
use DateTimeImmutable;

final class ProjetoDomain
{
    /**
     * @param  array{id: int, nome: string}|null  $clienteLookup
     * @param  array{id: int, nome: string}|null  $timeLookup
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    private function __construct(
        private ?int $id,
        private string $nome,
        private string $sigla,
        private ?string $descricao,
        private int $clienteId,
        private int $usuarioId,
        private int $timeId,
        private ?DateTimeImmutable $dtInicio,
        private ?DateTimeImmutable $dtPrevista,
        private ?DateTimeImmutable $dtFim,
        private ?array $clienteLookup = null,
        private ?array $timeLookup = null,
        private ?array $usuarioLookup = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        string $nome,
        string $sigla,
        int $clienteId,
        int $usuarioId,
        int $timeId,
        ?string $descricao = null,
        ?DateTimeImmutable $dtInicio = null,
        ?DateTimeImmutable $dtPrevista = null,
        ?DateTimeImmutable $dtFim = null,
    ): self {
        return new self(
            id: null,
            nome: trim($nome),
            sigla: trim($sigla),
            descricao: self::nullableString($descricao),
            clienteId: $clienteId,
            usuarioId: $usuarioId,
            timeId: $timeId,
            dtInicio: $dtInicio,
            dtPrevista: $dtPrevista,
            dtFim: $dtFim,
        );
    }

    /**
     * @param  array{id: int, nome: string}|null  $clienteLookup
     * @param  array{id: int, nome: string}|null  $timeLookup
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    public static function reconstituir(
        int $id,
        string $nome,
        string $sigla,
        int $clienteId,
        int $usuarioId,
        int $timeId,
        ?string $descricao = null,
        ?DateTimeImmutable $dtInicio = null,
        ?DateTimeImmutable $dtPrevista = null,
        ?DateTimeImmutable $dtFim = null,
        ?array $clienteLookup = null,
        ?array $timeLookup = null,
        ?array $usuarioLookup = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ): self {
        return new self(
            id: $id,
            nome: $nome,
            sigla: $sigla,
            descricao: self::nullableString($descricao),
            clienteId: $clienteId,
            usuarioId: $usuarioId,
            timeId: $timeId,
            dtInicio: $dtInicio,
            dtPrevista: $dtPrevista,
            dtFim: $dtFim,
            clienteLookup: $clienteLookup,
            timeLookup: $timeLookup,
            usuarioLookup: $usuarioLookup,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function renomear(string $nome): void
    {
        $this->nome = trim($nome);
        $this->assertInvariantes();
    }

    public function alterarSigla(string $sigla): void
    {
        $this->sigla = trim($sigla);
        $this->assertInvariantes();
    }

    public function atualizarDescricao(?string $descricao): void
    {
        $this->descricao = self::nullableString($descricao);
    }

    public function moverParaCliente(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->assertInvariantes();
    }

    public function reagendar(
        ?DateTimeImmutable $dtInicio,
        ?DateTimeImmutable $dtPrevista,
        ?DateTimeImmutable $dtFim = null,
    ): void {
        $this->dtInicio = $dtInicio;
        $this->dtPrevista = $dtPrevista;
        $this->dtFim = $dtFim;
    }

    /**
     * Aplica atualização completa a partir de dados já validados (API).
     *
     * @param  array<string, mixed>  $data
     */
    public function aplicarAtualizacao(array $data): void
    {
        if (array_key_exists('nome', $data)) {
            $this->renomear((string) $data['nome']);
        }

        if (array_key_exists('sigla', $data)) {
            $this->alterarSigla((string) $data['sigla']);
        }

        if (array_key_exists('descricao', $data)) {
            $this->atualizarDescricao($data['descricao'] !== null ? (string) $data['descricao'] : null);
        }

        if (array_key_exists('cliente_id', $data)) {
            $this->moverParaCliente((int) $data['cliente_id']);
        }

        $temDatas = array_key_exists('dt_inicio', $data)
            || array_key_exists('dt_prevista', $data)
            || array_key_exists('dt_fim', $data);

        if ($temDatas) {
            $this->reagendar(
                $this->parseDate($data['dt_inicio'] ?? $this->dtInicio?->format('Y-m-d')),
                $this->parseDate($data['dt_prevista'] ?? $this->dtPrevista?->format('Y-m-d')),
                $this->parseDate($data['dt_fim'] ?? $this->dtFim?->format('Y-m-d')),
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'descricao' => $this->descricao,
            'cliente_id' => $this->clienteId,
            'usuario_id' => $this->usuarioId,
            'time_id' => $this->timeId,
            'dt_inicio' => $this->dtInicio?->format('Y-m-d'),
            'dt_prevista' => $this->dtPrevista?->format('Y-m-d'),
            'dt_fim' => $this->dtFim?->format('Y-m-d'),
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

    public function getSigla(): string
    {
        return $this->sigla;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getClienteId(): int
    {
        return $this->clienteId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getTimeId(): int
    {
        return $this->timeId;
    }

    public function getDtInicio(): ?string
    {
        return $this->dtInicio?->format('Y-m-d');
    }

    public function getDtPrevista(): ?string
    {
        return $this->dtPrevista?->format('Y-m-d');
    }

    public function getDtFim(): ?string
    {
        return $this->dtFim?->format('Y-m-d');
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getCliente(): ?array
    {
        return $this->clienteLookup;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getTime(): ?array
    {
        return $this->timeLookup;
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
        if ($this->nome === '') {
            throw ProjetoDomainException::nomeObrigatorio();
        }

        if ($this->sigla === '') {
            throw ProjetoDomainException::siglaObrigatoria();
        }

        if ($this->clienteId <= 0) {
            throw ProjetoDomainException::clienteIdInvalido();
        }

        if ($this->usuarioId <= 0) {
            throw ProjetoDomainException::usuarioIdInvalido();
        }

        if ($this->timeId <= 0) {
            throw ProjetoDomainException::timeIdInvalido();
        }
    }

    private function parseDate(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        return new DateTimeImmutable((string) $value);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
