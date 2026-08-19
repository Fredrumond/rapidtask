<?php

namespace App\Exceptions;

use DomainException;

final class ClienteDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome do cliente é obrigatório.');
    }
}
