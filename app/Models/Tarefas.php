<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefas extends Model
{
    protected $table = 'tarefas';
    protected $fillable = ['titulo','tipo_id','situacao_id','prioridade_id']; 
}
