<?php

namespace App\Exceptions;

use DomainException;

final class TokenDomainException extends DomainException
{
    public static function jaRevogado(): self
    {
        return new self('O token de API já está revogado.');
    }

    public static function textoPlanoObrigatorio(): self
    {
        return new self('O texto do token é obrigatório.');
    }

    public static function textoPlanoEmInativo(): self
    {
        return new self('Não é possível anexar o texto a um token inativo.');
    }
}
