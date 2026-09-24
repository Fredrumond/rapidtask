<?php

namespace App\Models;

use Database\Factories\DuvidaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duvida extends Model
{
    /** @use HasFactory<DuvidaFactory> */
    use HasFactory;

    protected $table = 'duvidas';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'duvida',
    ];
}
