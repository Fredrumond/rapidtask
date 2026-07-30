<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'tipos';

    protected $fillable = [
        'nome',
    ];

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'tipo_id');
    }
}
