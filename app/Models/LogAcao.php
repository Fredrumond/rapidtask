<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogAcao extends Model
{
    use HasFactory;

    protected $table = 'log_acao';

    protected $fillable = [
        'nome',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'log_acao_id');
    }
}
