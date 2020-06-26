<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tipos extends Model
{
	protected $table = 'tipos';
	protected $fillable = ['id','nome'];
}
