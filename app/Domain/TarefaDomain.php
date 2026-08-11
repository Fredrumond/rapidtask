<?php

namespace App\Domain;

use App\Exceptions\TarefaDomainException;
use App\Enums\TarefaSituacao;
use App\Enums\TarefaStatus;
use DateTimeImmutable;

class TarefaDomain
{
    /**
     * @param  array{id: int, nome: string}|null  $tipoLookup
     * @param  array{id: int, nome: string}|null  $situacaoLookup
     * @param  array{id: int, nome: string}|null  $prioridadeLookup
     * @param  array{id: int, nome: string}|null  $projetoLookup
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    private function __construct(
        private ?int $id,
        private string $titulo,
        private ?string $descricao,
        private int $projetoId,
        private int $usuarioId,
        private int $tipoId,
        private int $prioridadeId,
        private TarefaSituacao $situacao,
        private TarefaStatus $status,
        private ?DateTimeImmutable $dtInicio,
        private ?DateTimeImmutable $dtPrevista,
        private ?DateTimeImmutable $dtFim,
        private ?int $tempoEstimado,
        private ?array $tipoLookup = null,
        private ?array $situacaoLookup = null,
        private ?array $prioridadeLookup = null,
        private ?array $projetoLookup = null,
        private ?array $usuarioLookup = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        string $titulo,
        int $projetoId,
        int $usuarioId,
        int $tipoId,
        int $prioridadeId,
        TarefaSituacao $situacao = TarefaSituacao::Novo,
        ?string $descricao = null,
        ?DateTimeImmutable $dtInicio = null,
        ?DateTimeImmutable $dtPrevista = null,
        ?DateTimeImmutable $dtFim = null,
        ?int $tempoEstimado = null,
    ): self {
        if ($situacao === TarefaSituacao::Finalizado && $dtFim === null) {
            $dtFim = new DateTimeImmutable('today');
        }

        return new self(
            id: null,
            titulo: trim($titulo),
            descricao: $descricao,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            tipoId: $tipoId,
            prioridadeId: $prioridadeId,
            situacao: $situacao,
            status: TarefaStatus::Ativa,
            dtInicio: $dtInicio,
            dtPrevista: $dtPrevista,
            dtFim: $dtFim,
            tempoEstimado: $tempoEstimado,
        );
    }

    /**
     * @param  array{id: int, nome: string}|null  $tipoLookup
     * @param  array{id: int, nome: string}|null  $situacaoLookup
     * @param  array{id: int, nome: string}|null  $prioridadeLookup
     * @param  array{id: int, nome: string}|null  $projetoLookup
     * @param  array{id: int, name: string}|null  $usuarioLookup
     */
    public static function reconstituir(
        int $id,
        string $titulo,
        ?string $descricao,
        int $projetoId,
        int $usuarioId,
        int $tipoId,
        int $prioridadeId,
        TarefaSituacao $situacao,
        TarefaStatus $status,
        ?DateTimeImmutable $dtInicio,
        ?DateTimeImmutable $dtPrevista,
        ?DateTimeImmutable $dtFim,
        ?int $tempoEstimado,
        ?array $tipoLookup = null,
        ?array $situacaoLookup = null,
        ?array $prioridadeLookup = null,
        ?array $projetoLookup = null,
        ?array $usuarioLookup = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ): self {
        return new self(
            id: $id,
            titulo: $titulo,
            descricao: $descricao,
            projetoId: $projetoId,
            usuarioId: $usuarioId,
            tipoId: $tipoId,
            prioridadeId: $prioridadeId,
            situacao: $situacao,
            status: $status,
            dtInicio: $dtInicio,
            dtPrevista: $dtPrevista,
            dtFim: $dtFim,
            tempoEstimado: $tempoEstimado,
            tipoLookup: $tipoLookup,
            situacaoLookup: $situacaoLookup,
            prioridadeLookup: $prioridadeLookup,
            projetoLookup: $projetoLookup,
            usuarioLookup: $usuarioLookup,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function renomear(string $titulo): void
    {
        $this->assertAtiva('renomear');
        $this->titulo = trim($titulo);
        $this->assertInvariantes();
    }

    public function atualizarDescricao(?string $descricao): void
    {
        $this->assertAtiva('atualizar descrição');
        $this->descricao = $descricao;
    }

    public function reagendar(
        ?DateTimeImmutable $dtInicio,
        ?DateTimeImmutable $dtPrevista,
        ?DateTimeImmutable $dtFim = null,
    ): void {
        $this->assertAtiva('reagendar');
        $this->dtInicio = $dtInicio;
        $this->dtPrevista = $dtPrevista;
        $this->dtFim = $dtFim;
        $this->assertInvariantes();
    }

    public function mudarSituacao(TarefaSituacao $nova): void
    {
        $this->assertAtiva('mudar situação');

        if (! $this->situacao->podeTransicionarPara($nova)) {
            throw TarefaDomainException::situacaoInvalida();
        }

        $this->situacao = $nova;

        if ($nova === TarefaSituacao::Finalizado && $this->dtFim === null) {
            $this->dtFim = new DateTimeImmutable('today');
        }

        $this->assertInvariantes();
    }

    public function finalizar(?DateTimeImmutable $dtFim = null): void
    {
        $this->mudarSituacao(TarefaSituacao::Finalizado);
        $this->dtFim = $dtFim ?? $this->dtFim ?? new DateTimeImmutable('today');
        $this->assertInvariantes();
    }

    public function arquivar(): void
    {
        if ($this->status === TarefaStatus::Arquivada) {
            throw TarefaDomainException::jaArquivada();
        }

        $this->status = TarefaStatus::Arquivada;
    }

    public function recuperar(): void
    {
        if ($this->status !== TarefaStatus::Arquivada) {
            throw TarefaDomainException::naoArquivada();
        }

        $this->status = TarefaStatus::Ativa;
    }

    public function estimar(?int $minutos): void
    {
        $this->assertAtiva('estimar');

        if ($minutos !== null && $minutos < 0) {
            throw TarefaDomainException::tempoEstimadoInvalido();
        }

        $this->tempoEstimado = $minutos;
    }

    public function moverParaProjeto(int $projetoId): void
    {
        $this->assertAtiva('mover projeto');
        $this->projetoId = $projetoId;
    }

    public function atribuirA(int $usuarioId): void
    {
        $this->assertAtiva('atribuir');
        $this->usuarioId = $usuarioId;
    }

    public function alterarTipo(int $tipoId): void
    {
        $this->assertAtiva('alterar tipo');
        $this->tipoId = $tipoId;
    }

    public function alterarPrioridade(int $prioridadeId): void
    {
        $this->assertAtiva('alterar prioridade');
        $this->prioridadeId = $prioridadeId;
    }

    /**
     * Aplica atualização completa a partir de dados já validados (API/web).
     *
     * @param  array<string, mixed>  $data
     */
    public function aplicarAtualizacao(array $data): void
    {
        if (array_key_exists('status', $data) && $data['status'] !== null) {
            $novoStatus = TarefaStatus::from((int) $data['status']);

            if ($novoStatus === TarefaStatus::Ativa && $this->status === TarefaStatus::Arquivada) {
                $this->recuperar();
            }
        }

        if (array_key_exists('titulo', $data)) {
            $this->renomear((string) $data['titulo']);
        }

        if (array_key_exists('descricao', $data)) {
            $this->atualizarDescricao($data['descricao'] !== null ? (string) $data['descricao'] : null);
        }

        if (array_key_exists('projeto_id', $data)) {
            $this->moverParaProjeto((int) $data['projeto_id']);
        }

        if (array_key_exists('tipo_id', $data)) {
            $this->alterarTipo((int) $data['tipo_id']);
        }

        if (array_key_exists('prioridade_id', $data)) {
            $this->alterarPrioridade((int) $data['prioridade_id']);
        }

        if (array_key_exists('tempo_estimado', $data)) {
            $this->estimar($data['tempo_estimado'] !== null ? (int) $data['tempo_estimado'] : null);
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

        if (array_key_exists('situacao_id', $data)) {
            $this->mudarSituacao(TarefaSituacao::from((int) $data['situacao_id']));
        }

        if (array_key_exists('status', $data) && $data['status'] !== null) {
            $novoStatus = TarefaStatus::from((int) $data['status']);

            if ($novoStatus === TarefaStatus::Arquivada && $this->status === TarefaStatus::Ativa) {
                $this->arquivar();
            }
        }
    }

    public function isAtiva(): bool
    {
        return $this->status === TarefaStatus::Ativa;
    }

    public function isArquivada(): bool
    {
        return $this->status === TarefaStatus::Arquivada;
    }

    public function isFinalizada(): bool
    {
        return $this->situacao === TarefaSituacao::Finalizado;
    }

    public function isAtrasada(?DateTimeImmutable $hoje = null): bool
    {
        $hoje ??= new DateTimeImmutable('today');

        return $this->isAtiva()
            && ! $this->isFinalizada()
            && $this->dtPrevista !== null
            && $this->dtPrevista < $hoje;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'projeto_id' => $this->projetoId,
            'usuario_id' => $this->usuarioId,
            'tipo_id' => $this->tipoId,
            'prioridade_id' => $this->prioridadeId,
            'situacao_id' => $this->situacao->value,
            'status' => $this->status->value,
            'dt_inicio' => $this->dtInicio?->format('Y-m-d'),
            'dt_prevista' => $this->dtPrevista?->format('Y-m-d'),
            'dt_fim' => $this->dtFim?->format('Y-m-d'),
            'tempo_estimado' => $this->tempoEstimado,
        ];
    }

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

    public function getTempoEstimado(): ?int
    {
        return $this->tempoEstimado;
    }

    public function getStatus(): int
    {
        return $this->status->value;
    }

    public function getStatusEnum(): TarefaStatus
    {
        return $this->status;
    }

    public function getSituacaoEnum(): TarefaSituacao
    {
        return $this->situacao;
    }

    public function getProjetoId(): int
    {
        return $this->projetoId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getTipoId(): int
    {
        return $this->tipoId;
    }

    public function getPrioridadeId(): int
    {
        return $this->prioridadeId;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getTipo(): ?array
    {
        return $this->tipoLookup;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getSituacao(): ?array
    {
        return $this->situacaoLookup ?? ['id' => $this->situacao->value, 'nome' => $this->situacao->name];
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getPrioridade(): ?array
    {
        return $this->prioridadeLookup;
    }

    /**
     * @return array{id: int, nome: string}|null
     */
    public function getProjeto(): ?array
    {
        return $this->projetoLookup;
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
        if ($this->titulo === '') {
            throw TarefaDomainException::tituloObrigatorio();
        }

        if ($this->dtInicio !== null && $this->dtPrevista !== null && $this->dtInicio > $this->dtPrevista) {
            throw TarefaDomainException::datasInvalidas('início depois da prevista');
        }

        if ($this->situacao === TarefaSituacao::Finalizado && $this->dtFim === null) {
            throw TarefaDomainException::datasInvalidas('finalizada sem dt_fim');
        }

        if ($this->tempoEstimado !== null && $this->tempoEstimado < 0) {
            throw TarefaDomainException::tempoEstimadoInvalido();
        }
    }

    private function assertAtiva(string $operacao): void
    {
        if ($this->isArquivada()) {
            throw TarefaDomainException::operacaoEmArquivada($operacao);
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
}
