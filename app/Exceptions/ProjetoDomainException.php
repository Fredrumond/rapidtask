<?php

namespace App\Exceptions;

use DomainException;

final class ProjetoDomainException extends DomainException
{
    public static function nomeObrigatorio(): self
    {
        return new self('O nome do projeto é obrigatório.');
    }

    public static function siglaObrigatoria(): self
    {
        return new self('A sigla do projeto é obrigatória.');
    }

    public static function clienteIdInvalido(): self
    {
        return new self('O cliente do projeto é inválido.');
    }

    public static function usuarioIdInvalido(): self
    {
        return new self('O responsável do projeto é inválido.');
    }

    public static function timeIdInvalido(): self
    {
        return new self('O time do projeto é inválido.');
    }
}
