<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Prioridades extends Model
{
	protected $table = 'prioridades';
	protected $fillable = ['id','nome'];
}
