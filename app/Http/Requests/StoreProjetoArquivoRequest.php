<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreProjetoArquivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ProjetoArquivo::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'projeto_id' => ['required', 'exists:projetos,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'arquivo' => [
                'required',
                File::types(['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'xls', 'xlsx', 'txt'])
                    ->max(10 * 1024),
            ],
        ];
    }
}
