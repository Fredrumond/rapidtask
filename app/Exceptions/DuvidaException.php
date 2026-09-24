<?php

namespace App\Exceptions;

use Exception;

class DuvidaException extends Exception
{
    public static function createFailed(): self
    {
        return new self('Não foi possível registrar a dúvida.');
    }
}
