<?php

namespace App\Domain;

use App\Exceptions\ClienteDomainException;

final class ClienteDomain
{
    private function __construct(
        private ?int $id,
        private string $nome,
        private ?string $email,
        private ?string $telefone,
        private int $usuarioId,
        private int $timeId,
    ) {
        $this->assertInvariantes();
    }

    public static function criar(
        string $nome,
        int $usuarioId,
        int $timeId,
        ?string $email = null,
        ?string $telefone = null,
    ): self {
        return new self(
            id: null,
            nome: trim($nome),
            email: self::nullableString($email),
            telefone: self::nullableString($telefone),
            usuarioId: $usuarioId,
            timeId: $timeId,
        );
    }

    public static function reconstituir(
        int $id,
        string $nome,
        int $usuarioId,
        int $timeId,
        ?string $email = null,
        ?string $telefone = null,
    ): self {
        return new self(
            id: $id,
            nome: $nome,
            email: self::nullableString($email),
            telefone: self::nullableString($telefone),
            usuarioId: $usuarioId,
            timeId: $timeId,
        );
    }

    public function renomear(string $nome): void
    {
        $this->nome = trim($nome);
        $this->assertInvariantes();
    }

    public function atualizarContato(?string $email, ?string $telefone): void
    {
        $this->email = self::nullableString($email);
        $this->telefone = self::nullableString($telefone);
    }

    /**
     * Aplica atualização completa a partir de dados já validados (web).
     *
     * @param  array<string, mixed>  $data
     */
    public function aplicarAtualizacao(array $data): void
    {
        if (array_key_exists('nome', $data)) {
            $this->renomear((string) $data['nome']);
        }

        $temContato = array_key_exists('email', $data) || array_key_exists('telefone', $data);

        if ($temContato) {
            $this->atualizarContato(
                array_key_exists('email', $data) ? (isset($data['email']) ? (string) $data['email'] : null) : $this->email,
                array_key_exists('telefone', $data) ? (isset($data['telefone']) ? (string) $data['telefone'] : null) : $this->telefone,
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
            'email' => $this->email,
            'telefone' => $this->telefone,
            'usuario_id' => $this->usuarioId,
            'time_id' => $this->timeId,
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getTimeId(): int
    {
        return $this->timeId;
    }

    private function assertInvariantes(): void
    {
        if ($this->nome === '') {
            throw ClienteDomainException::nomeObrigatorio();
        }
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
