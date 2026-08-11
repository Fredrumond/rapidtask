<?php

namespace App\Exceptions;

use Exception;

class TarefaComentarioException extends Exception
{
    public static function notFound(): self
    {
        return new self('Comentário não encontrado.');
    }

    public static function tarefaNotFound(): self
    {
        return new self('Tarefa não encontrada.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar o comentário.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar o comentário.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir o comentário.');
    }
}
