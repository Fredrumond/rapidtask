<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeMembroConvite extends Model
{
	protected $table = 'membro_time_convite';
	protected $fillable = ['nome','email','time','status'];

	
}
