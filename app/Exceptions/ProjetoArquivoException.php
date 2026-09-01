<?php

namespace App\Exceptions;

use Exception;

class ProjetoArquivoException extends Exception
{
    public static function notFound(): self
    {
        return new self('Arquivo não encontrado.');
    }

    public static function projetoNotFound(): self
    {
        return new self('Projeto não encontrado.');
    }

    public static function createFailed(): self
    {
        return new self('Não foi possível enviar o arquivo.');
    }

    public static function deleteFailed(): self
    {
        return new self('Não foi possível excluir o arquivo.');
    }
}
