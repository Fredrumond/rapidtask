<?php

namespace App\Enums;

enum TokenStatus: string
{
    case Inativo = 'inativo';
    case Ativo = 'ativo';

    public function podeRevogar(): bool
    {
        return $this === self::Ativo;
    }

    public function podeAnexarTextoPlano(): bool
    {
        return $this === self::Ativo;
    }
}
