<?php

namespace App\Exceptions;

use Exception;

class ContaException extends Exception
{
    public static function notFound(): self
    {
        return new self('Conta não encontrada.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar a conta.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar a conta.');
    }
}
