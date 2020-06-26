<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Time extends Model
{
	protected $table = 'time';
	protected $fillable = ['nome','logo','usuario_id'];


	public function listaTodosTimes($id)
	{
		$select = "SELECT
		t.id,
		t.nome,
		t.logo
		FROM
		time t
		INNER JOIN time_membro tm ON
		tm.time_id = t.id
		WHERE
		tm.usuario_id = '" . $id . "'";

		return $times = collect(DB::select($select))->all();
	}

	
}
