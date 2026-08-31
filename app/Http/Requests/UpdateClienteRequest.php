<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Models\Conta;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user() instanceof Conta) {
            return false;
        }

        $cliente = Cliente::query()->find($this->route('cliente_id'));

        if ($cliente === null) {
            return true;
        }

        return $this->user()->can('update', $cliente) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'time_id' => ['required', 'integer', 'min:1'],
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'telefone' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function clienteAttributes(): array
    {
        return $this->safe()->except(['time_id']);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Erro de validação',
            'errors' => $validator->errors(),
        ], 422));
    }
}
