<?php

namespace App\Http\Requests;

use App\Models\Conta;
use App\Models\ProjetoArquivo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreProjetoArquivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ProjetoArquivo::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isApi = $this->user() instanceof Conta;

        return [
            'time_id' => $isApi ? ['required', 'integer', 'min:1'] : ['sometimes', 'integer', 'min:1'],
            'projeto_id' => $isApi ? ['sometimes', 'integer'] : ['required', 'exists:projetos,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'arquivo' => [
                'required',
                File::types(['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'xls', 'xlsx', 'txt'])
                    ->max(10 * 1024),
            ],
        ];
    }

    /**
     * @return array{nome: string, descricao: string, arquivo: \Illuminate\Http\UploadedFile}
     */
    public function arquivoAttributes(): array
    {
        /** @var array{nome: string, descricao: string, arquivo: \Illuminate\Http\UploadedFile} $attributes */
        $attributes = $this->safe()->only(['nome', 'descricao', 'arquivo']);

        return $attributes;
    }
}
