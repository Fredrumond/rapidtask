<?php

namespace App\Exceptions;

use Exception;

class TokenException extends Exception
{
    public static function revokeFailed(): self
    {
        return new self('Não foi possível revogar o token de API.');
    }

    public static function issueFailed(): self
    {
        return new self('Não foi possível gerar o token de API.');
    }
}
