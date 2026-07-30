<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    protected $table = 'log';

    protected $fillable = [
        'log_acao_id',
        'log_tipo_id',
        'usuario_id',
        'identificacao',
    ];

    protected function casts(): array
    {
        return [
            'identificacao' => 'integer',
        ];
    }

    public function acao(): BelongsTo
    {
        return $this->belongsTo(LogAcao::class, 'log_acao_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(LogTipo::class, 'log_tipo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
