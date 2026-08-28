<?php

namespace App\Exceptions;

use Exception;

class ClienteException extends Exception
{
    public static function notFound(): self
    {
        return new self('Cliente não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar o cliente.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar o cliente.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir o cliente.');
    }

    public static function operationFailed(): self
    {
        return new self('Não foi possível realizar a operação no cliente.');
    }
}
