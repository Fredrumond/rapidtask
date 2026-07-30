<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeamViaProjeto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarefa extends Model
{
    use BelongsToTeamViaProjeto, HasFactory, SoftDeletes;

    protected $table = 'tarefas';

    protected $fillable = [
        'tipo_id',
        'titulo',
        'descricao',
        'situacao_id',
        'prioridade_id',
        'dt_inicio',
        'dt_prevista',
        'dt_fim',
        'tempo_estimado',
        'status',
        'projeto_id',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'dt_inicio' => 'date',
            'dt_prevista' => 'date',
            'dt_fim' => 'date',
            'tempo_estimado' => 'integer',
            'status' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function situacao(): BelongsTo
    {
        return $this->belongsTo(Situacao::class, 'situacao_id');
    }

    public function prioridade(): BelongsTo
    {
        return $this->belongsTo(Prioridade::class, 'prioridade_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(TarefaComentario::class, 'tarefa_id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(TarefaHistorico::class, 'tarefa_id');
    }
}
