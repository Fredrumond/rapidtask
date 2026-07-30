<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projeto extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $table = 'projetos';

    protected $fillable = [
        'nome',
        'descricao',
        'sigla',
        'cliente_id',
        'usuario_id',
        'time_id',
        'dt_inicio',
        'dt_prevista',
        'dt_fim',
    ];

    protected function casts(): array
    {
        return [
            'dt_inicio' => 'date',
            'dt_prevista' => 'date',
            'dt_fim' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'projeto_id');
    }

    public function arquivos(): HasMany
    {
        return $this->hasMany(ProjetoArquivo::class, 'projeto_id');
    }

    public function anotacoes(): HasMany
    {
        return $this->hasMany(ProjetoAnotacao::class, 'projeto_id');
    }
}
