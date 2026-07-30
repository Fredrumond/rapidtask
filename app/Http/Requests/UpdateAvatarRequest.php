<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                File::image()->max(2 * 1024)->types(['jpg', 'jpeg', 'png', 'webp']),
            ],
        ];
    }
}
