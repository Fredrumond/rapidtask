<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeamViaTarefa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarefaHistorico extends Model
{
    use BelongsToTeamViaTarefa, HasFactory;

    protected $table = 'tarefa_historico';

    protected $fillable = [
        'tarefa_id',
        'usuario_id',
        'atividade',
    ];

    public function tarefa(): BelongsTo
    {
        return $this->belongsTo(Tarefa::class, 'tarefa_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
