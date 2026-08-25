<?php

namespace App\Exceptions;

use Exception;

class ProjetoException extends Exception
{
    public static function notFound(): self
    {
        return new self('Projeto não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar o projeto.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar o projeto.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir o projeto.');
    }
}
