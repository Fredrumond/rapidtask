<?php

namespace App\Exceptions;

use DomainException;

final class ConviteDomainException extends DomainException
{
    private function __construct(
        string $message,
        private readonly bool $gone = false,
    ) {
        parent::__construct($message);
    }

    public function isGone(): bool
    {
        return $this->gone;
    }

    public static function nomeObrigatorio(): self
    {
        return new self('O nome do convidado é obrigatório.');
    }

    public static function emailObrigatorio(): self
    {
        return new self('O e-mail do convidado é obrigatório.');
    }

    public static function tokenObrigatorio(): self
    {
        return new self('O token do convite é obrigatório.');
    }

    public static function timeIdInvalido(): self
    {
        return new self('O time do convite é inválido.');
    }

    public static function emailJaPertenceAOutraConta(): self
    {
        return new self('Este e-mail já pertence a outra conta na plataforma.');
    }

    public static function usuarioJaPertenceAOutraConta(): self
    {
        return new self('Você já pertence a outra conta na plataforma.');
    }

    public static function conviteNaoPendente(): self
    {
        return new self('Convite já utilizado.', gone: true);
    }

    public static function emailNaoCoincidente(): self
    {
        return new self('Este convite é para outro e-mail.');
    }

    public static function conviteExpirado(): self
    {
        return new self('Este convite expirou.', gone: true);
    }
}
