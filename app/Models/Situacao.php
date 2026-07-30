<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Situacao extends Model
{
    use HasFactory;

    protected $table = 'situacoes';

    protected $fillable = [
        'nome',
    ];

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'situacao_id');
    }
}
