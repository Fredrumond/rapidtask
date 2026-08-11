<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\TarefaComentario;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTarefaComentarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user() instanceof Conta) {
            return false;
        }

        $comentario = TarefaComentario::query()
            ->where('tarefa_id', $this->route('tarefa_id'))
            ->find($this->route('comentario_id'));

        if ($comentario === null) {
            return true;
        }

        return $this->user()->can('update', $comentario) ?? false;
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
