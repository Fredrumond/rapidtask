<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\ProjetoAnotacao;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjetoAnotacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Conta
            && ($this->user()->can('create', ProjetoAnotacao::class) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'time_id' => ['required', 'integer', 'min:1'],
            'anotacao' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function anotacaoAttributes(): array
    {
        return $this->safe()->except(['time_id']);
    }
}
