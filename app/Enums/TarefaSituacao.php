<?php

namespace App\Enums;

enum TarefaSituacao: int
{
    case Novo = 1;
    case Andamento = 2;
    case EmEspera = 3;
    case Finalizado = 4;

    public function podeTransicionarPara(self $destino): bool
    {
        if ($this === $destino) {
            return true;
        }

        return match ($this) {
            self::Novo => in_array($destino, [self::Andamento, self::EmEspera, self::Finalizado], true),
            self::Andamento => in_array($destino, [self::EmEspera, self::Finalizado, self::Novo], true),
            self::EmEspera => in_array($destino, [self::Andamento, self::Finalizado], true),
            self::Finalizado => false,
        };
    }
}
