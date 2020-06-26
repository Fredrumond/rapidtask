<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjetosAnotacoes extends Model
{
	protected $table = 'projetos_anotacoes';
	protected $fillable = ['id','projeto_id','usuario_id','anotacao'];

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
}
