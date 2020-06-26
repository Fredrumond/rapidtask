<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LogTipo extends Model
{
	protected $table = 'log_tipo';
	protected $fillable = ['nome'];

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
