<?php

namespace App\Exceptions;

use DomainException;

final class TarefaDomainException extends DomainException
{
    public static function tituloObrigatorio(): self
    {
        return new self('O título da tarefa é obrigatório.');
    }

    public static function jaArquivada(): self
    {
        return new self('A tarefa já está arquivada.');
    }

    public static function naoArquivada(): self
    {
        return new self('A tarefa não está arquivada.');
    }

    public static function datasInvalidas(string $motivo): self
    {
        return new self('Datas inválidas: '.$motivo);
    }

    public static function situacaoInvalida(): self
    {
        return new self('Transição de situação não permitida.');
    }

    public static function operacaoEmArquivada(string $operacao): self
    {
        return new self("Não é possível {$operacao} uma tarefa arquivada.");
    }

    public static function tempoEstimadoInvalido(): self
    {
        return new self('O tempo estimado não pode ser negativo.');
    }
}
