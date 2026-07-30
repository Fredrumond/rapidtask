<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeamViaTarefa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarefaComentario extends Model
{
    use BelongsToTeamViaTarefa, HasFactory, SoftDeletes;

    protected $table = 'tarefa_comentario';

    protected $fillable = [
        'tarefa_id',
        'usuario_id',
        'comentario',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function tarefa(): BelongsTo
    {
        return $this->belongsTo(Tarefa::class, 'tarefa_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
