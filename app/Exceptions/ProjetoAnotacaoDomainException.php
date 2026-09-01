<?php

namespace App\Exceptions;

use DomainException;

final class ProjetoAnotacaoDomainException extends DomainException
{
    public static function anotacaoObrigatoria(): self
    {
        return new self('A anotação é obrigatória.');
    }
}
