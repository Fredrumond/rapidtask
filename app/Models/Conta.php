<?php

namespace App\Models;

use Database\Factories\ContaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conta extends Model
{
    /** @use HasFactory<ContaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'conta';

    protected $fillable = [
        'nome',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function times(): HasMany
    {
        return $this->hasMany(Time::class, 'conta_id');
    }
}
