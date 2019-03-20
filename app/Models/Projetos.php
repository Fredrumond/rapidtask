<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Projetos extends Model
{
	protected $table = 'projetos';
	protected $fillable = ['nome','descricao','sigla','cliente_id'];

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
	
}
