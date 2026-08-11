<?php

namespace App\Exceptions;

use DomainException;

final class TarefaComentarioDomainException extends DomainException
{
    public static function comentarioObrigatorio(): self
    {
        return new self('O comentário é obrigatório.');
    }
}
