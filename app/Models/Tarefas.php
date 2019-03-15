<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarefas extends Model
{
	protected $table = 'tarefas';
	protected $fillable = ['titulo','tipo_id','situacao_id','prioridade_id','descricao','dt_inicio','dt_prevista','tempo_estimado'];

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
}
