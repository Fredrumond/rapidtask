<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\TarefaComentario;
use Illuminate\Foundation\Http\FormRequest;

class StoreTarefaComentarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Conta
            && ($this->user()->can('create', TarefaComentario::class) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'time_id' => ['required', 'integer', 'min:1'],
            'comentario' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function comentarioAttributes(): array
    {
        return $this->safe()->except(['time_id']);
    }
}
