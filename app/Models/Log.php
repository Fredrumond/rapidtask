<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Log extends Model
{
	protected $table = 'log';
	protected $fillable = ['log_acao_id','log_tipo_id','usuario_id','identficacao'];

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

	public function listaLogs($id)
	{
		DB::statement("SET lc_time_names = 'pt_BR'");	

		$select = "SELECT
		(CASE 
		WHEN log_acao_id = 1 THEN 'Registrou'
		WHEN log_acao_id = 2 THEN 'Atualizou'
		WHEN log_acao_id = 3 THEN 'Excluiu'
		WHEN log_acao_id = 4 THEN 'Arquivou'
		END) as acao,
		(CASE 
		WHEN log_tipo_id = 1 THEN 'Cliente'
		WHEN log_tipo_id = 2 THEN 'Projeto'
		WHEN log_tipo_id = 3 THEN 'Atividade'
		WHEN log_tipo_id = 4 THEN 'Tarefa'
		WHEN log_tipo_id = 5 THEN 'Time'
		END) as tipo,
		DATE_FORMAT(created_at, '%d %M, %Y às %Hh%i') as data
		FROM log 
		WHERE usuario_id = '" . $id . "'
		ORDER BY UNIX_TIMESTAMP(created_at) DESC
		LIMIT 5";		
		
		return $logs = collect(DB::select($select))->all();
	}
}
