<?php

namespace App\Exceptions;

use Exception;

class TarefaException extends Exception
{
    public static function notFound(): self
    {
        return new self('Tarefa não encontrada.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar a tarefa.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar a tarefa.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir a tarefa.');
    }
}
