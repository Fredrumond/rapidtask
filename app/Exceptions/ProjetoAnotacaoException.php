<?php

namespace App\Exceptions;

use Exception;

class ProjetoAnotacaoException extends Exception
{
    public static function notFound(): self
    {
        return new self('Anotação não encontrada.');
    }

    public static function projetoNotFound(): self
    {
        return new self('Projeto não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível criar a anotação.');
    }

    public static function updateFailed(): self
    {
        return new self('Não foi possível atualizar a anotação.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir a anotação.');
    }
}
