<?php

namespace App\Exceptions;

use DomainException;

final class ContaDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome da conta é obrigatório.');
    }

    public static function usuarioIdInvalido(): self
    {
        return new self('O proprietário da conta é inválido.');
    }

    public static function apenasOwnerPodeRenomear(): self
    {
        return new self('Apenas o proprietário pode alterar o nome da conta.');
    }
}
