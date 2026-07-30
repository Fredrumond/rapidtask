<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeNivel extends Model
{
    use HasFactory;

    protected $table = 'time_nivel';

    protected $fillable = [
        'nome',
    ];

    public function membros(): HasMany
    {
        return $this->hasMany(TimeMembro::class, 'nivel_id');
    }
}
