<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Situacoes extends Model
{
	protected $table = 'situacoes';
	protected $fillable = ['id','nome'];
}
