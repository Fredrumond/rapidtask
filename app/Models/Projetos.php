<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Projetos extends Model
{
	protected $table = 'projetos';
	protected $fillable = ['nome','descricao','sigla','cliente_id','dt_inicio','dt_prevista','dt_fim'];

	public function getCreatedAtAttribute($value)
	{
		$data = Carbon::createFromFormat('Y-m-d H:i:s', $value);

		return $data->format('d/m/Y H:i');
	}

	public function getUpdatedAtAttribute($value)
	{
		$data = Carbon::createFromFormat('Y-m-d H:i:s', $value);

		return $data->format('d/m/Y H:i');
	}	

	public function cliente()
	{
		return $this->belongsTo(Clientes::class, 'cliente_id');
	}


	public function listaTodosProjetos($id)
	{
		$select = "SELECT
		p.id,
		p.nome,
		c.nome as cliente
		FROM
		projetos p
		INNER JOIN time_membro tm ON
		p.time_id = tm.time_id
		INNER JOIN clientes c ON
		p.cliente_id = c.id
		WHERE
		tm.usuario_id = '" . $id . "'";

		return $projetos = collect(DB::select($select))->all();
	}

	public function totalProjetos($id)
	{
		$select = "SELECT
		count(*) as total
		FROM
		projetos p
		INNER JOIN time_membro tm ON
		p.time_id = tm.time_id
		INNER JOIN clientes c ON
		p.cliente_id = c.id
		WHERE
		tm.usuario_id = '" . $id . "'";

		return $totalProjetos = collect(DB::select($select))->all();
	}
	
}
