<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeamViaProjeto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjetoAnotacao extends Model
{
    use BelongsToTeamViaProjeto, HasFactory, SoftDeletes;

    protected $table = 'projetos_anotacoes';

    protected $fillable = [
        'projeto_id',
        'usuario_id',
        'anotacao',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
