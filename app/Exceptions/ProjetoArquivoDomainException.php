<?php

namespace App\Exceptions;

use DomainException;

final class ProjetoArquivoDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome do arquivo é obrigatório.');
    }

    public static function descricaoObrigatoria(): self
    {
        return new self('A descrição do arquivo é obrigatória.');
    }

    public static function srcObrigatorio(): self
    {
        return new self('O caminho do arquivo é obrigatório.');
    }
}
