<?php

namespace App\Exceptions;

use Exception;

class TimeException extends Exception
{
    public static function notFound(): self
    {
        return new self('Time não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar o time.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir o time.');
    }
}
