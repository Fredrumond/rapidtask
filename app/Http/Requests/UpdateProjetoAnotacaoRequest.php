<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\ProjetoAnotacao;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjetoAnotacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user() instanceof Conta) {
            return false;
        }

        $anotacao = ProjetoAnotacao::query()
            ->where('projeto_id', $this->route('projeto_id'))
            ->find($this->route('anotacao_id'));

        if ($anotacao === null) {
            return true;
        }

        return $this->user()->can('update', $anotacao) ?? false;
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
