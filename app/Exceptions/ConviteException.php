<?php

namespace App\Exceptions;

use Exception;

class ConviteException extends Exception
{
    public static function notFound(): self
    {
        return new self('Convite não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível enviar o convite.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar o convite.');
    }
}
