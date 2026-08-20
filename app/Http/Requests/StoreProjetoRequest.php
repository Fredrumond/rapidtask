<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\Projeto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Conta
            && ($this->user()->can('create', Projeto::class) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $timeId = current_time_id();

        return [
            'time_id' => ['required', 'integer', 'min:1'],
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['required', 'string', 'max:20'],
            'cliente_id' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')->where(function ($query) use ($timeId) {
                    $query->where('time_id', $timeId)->whereNull('deleted_at');
                }),
            ],
            'descricao' => ['nullable', 'string'],
            'dt_inicio' => ['nullable', 'date'],
            'dt_prevista' => ['nullable', 'date'],
            'dt_fim' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function projetoAttributes(): array
    {
        return $this->safe()->except(['time_id']);
    }
}
