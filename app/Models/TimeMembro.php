<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeMembro extends Model
{
	protected $table = 'time_membro';
	protected $fillable = ['usuario_id','time_id','nivel_id'];


	public function membro()
	{
		return $this->hasMany('App\User', 'id', 'usuario_id');
	}

	public function nivel()
	{
		return $this->hasMany(TimeNivel::class, 'id', 'nivel_id');
	}

	public function time()
	{
		return $this->hasMany(Time::class, 'id', 'time_id');
	}

	
}
