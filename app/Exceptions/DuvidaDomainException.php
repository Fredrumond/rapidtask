<?php

namespace App\Exceptions;

use DomainException;

final class DuvidaDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome é obrigatório.');
    }

    public static function emailObrigatorio(): self
    {
        return new self('O e-mail é obrigatório.');
    }

    public static function emailInvalido(): self
    {
        return new self('O e-mail é inválido.');
    }

    public static function telefoneObrigatorio(): self
    {
        return new self('O telefone é obrigatório.');
    }

    public static function duvidaObrigatoria(): self
    {
        return new self('A dúvida é obrigatória.');
    }
}
