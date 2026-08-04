<?php

namespace App\Http\Requests;

use App\Models\Tarefa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tarefa = Tarefa::query()->find($this->route('tarefa_id'));

        if ($tarefa === null) {
            return true;
        }

        return $this->user()?->can('update', $tarefa) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $timeId = current_time_id();

        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'projeto_id' => [
                'required',
                'integer',
                Rule::exists('projetos', 'id')->where(function ($query) use ($timeId) {
                    $query->where('time_id', $timeId)->whereNull('deleted_at');
                }),
            ],
            'tipo_id' => ['required', 'integer', 'exists:tipos,id'],
            'situacao_id' => ['required', 'integer', 'exists:situacoes,id'],
            'prioridade_id' => ['required', 'integer', 'exists:prioridades,id'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
            'dt_fim' => ['nullable', 'date'],
            'tempo_estimado' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
