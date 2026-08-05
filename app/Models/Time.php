<?php

namespace App\Models;

use App\Models\Concerns\ScopedToMemberTeams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Time extends Model
{
    use HasFactory, ScopedToMemberTeams, SoftDeletes;

    protected $table = 'time';

    protected $fillable = [
        'nome',
        'logo',
        'usuario_id',
        'conta_id',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'time_id');
    }

    public function projetos(): HasMany
    {
        return $this->hasMany(Projeto::class, 'time_id');
    }

    public function membros(): HasMany
    {
        return $this->hasMany(TimeMembro::class, 'time_id');
    }

    public function convites(): HasMany
    {
        return $this->hasMany(TimeMembroConvite::class, 'time_id');
    }
}
