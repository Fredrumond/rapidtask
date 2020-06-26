<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Tarefas extends Model
{
	protected $table = 'tarefas';
	protected $fillable = ['titulo','tipo_id','situacao_id','prioridade_id','descricao','dt_inicio','dt_prevista','tempo_estimado','dt_fim','status','projeto_id'];

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

	public function tipo()
	{
		return $this->belongsTo(Tipos::class, 'tipo_id');
	}

	public function situacao()
	{
		return $this->belongsTo(Situacao::class, 'situacao_id');
	}

	public function prioridade()
	{
		return $this->belongsTo(Prioridade::class, 'prioridade_id');
	}

	public function projeto()
	{
		return $this->belongsTo(Projetos::class, 'projeto_id');
	}

	public function listaTodasTarefas($id)
	{
		$select = "SELECT	
		t.id as id,
		p.sigla as sigla,
		tp.nome as tipo,
		t.situacao_id,
		t.prioridade_id,
		t.titulo,
		DATE_FORMAT(t.updated_at, '%d/%m/%Y %H:%i') as updated_at
		FROM
		tarefas t
		INNER JOIN projetos p ON
		p.id = t.projeto_id
		INNER JOIN time ti ON
		ti.id = p.time_id
		INNER JOIN time_membro tm ON
		tm.time_id = ti.id
		INNER JOIN tipos tp on tp.id = t.tipo_id
		WHERE
		t.status = 0
		AND t.situacao_id <> 4
		AND tm.usuario_id = '" . $id . "'
		ORDER BY t.prioridade_id DESC";

		return $tarefas = collect(DB::select($select))->all();
	}

	public function totalTarefas($id)
	{
		$select = "SELECT	
		count(*) as total
		FROM
		tarefas t
		INNER JOIN projetos p ON
		p.id = t.projeto_id
		INNER JOIN time ti ON
		ti.id = p.time_id
		INNER JOIN time_membro tm ON
		tm.time_id = ti.id
		INNER JOIN tipos tp on tp.id = t.tipo_id
		WHERE
		t.status = 0 AND tm.usuario_id = '" . $id . "'";

		return $totalTarefas = collect(DB::select($select))->all();
	}
}
