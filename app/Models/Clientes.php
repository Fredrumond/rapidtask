<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clientes extends Model
{
	protected $table = 'clientes';
	protected $fillable = ['nome','email','telefone','usuario_id','time_id'];

	public function listaTodosClientes($id)
	{
		$select = "SELECT
		c.id,
		c.nome,
		c.email,
		c.telefone,
		c.usuario_id		
		FROM
		clientes c
		INNER JOIN time_membro tm ON
		tm.time_id = c.time_id
		WHERE
		tm.usuario_id = '" . $id . "'";
		
		return $clientes = collect(DB::select($select))->all();
	}

	public function totalClientes($id)
	{
		$select = "SELECT
		count(*) as total
		FROM
		clientes c
		INNER JOIN time_membro tm ON
		tm.time_id = c.time_id
		WHERE
		tm.usuario_id = '" . $id . "'";

		return $totalClientes = collect(DB::select($select))->all();
	}

	
}
