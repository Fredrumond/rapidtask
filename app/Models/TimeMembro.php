<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeMembro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'time_membro';

    protected $fillable = [
        'time_id',
        'usuario_id',
        'nivel_id',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(TimeNivel::class, 'nivel_id');
    }
}
