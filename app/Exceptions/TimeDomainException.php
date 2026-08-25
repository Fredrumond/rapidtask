<?php

namespace App\Exceptions;

use DomainException;

final class TimeDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome do time é obrigatório.');
    }

    public static function contaIdInvalido(): self
    {
        return new self('A conta do time é inválida.');
    }

    public static function criadorIdInvalido(): self
    {
        return new self('O criador do time é inválido.');
    }

    public static function apenasAdminPodeExcluir(): self
    {
        return new self('Apenas um administrador do time pode excluí-lo.');
    }
}
