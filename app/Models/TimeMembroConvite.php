<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeMembroConvite extends Model
{
    use HasFactory;

    protected $table = 'membro_time_convite';

    protected $fillable = [
        'nome',
        'email',
        'time_id',
        'token',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_id');
    }
}
