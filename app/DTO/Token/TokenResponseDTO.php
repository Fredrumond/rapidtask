<?php

namespace App\DTO\Token;

use JsonSerializable;

class TokenResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly bool $active,
        public readonly string $name,
        public readonly ?string $plainTextToken = null,
        public readonly ?string $createdAt = null,
    ) {}

    public function jsonSerialize(): array
    {
        $data = [
            'active' => $this->active,
            'name' => $this->name,
        ];

        if ($this->plainTextToken !== null) {
            $data['plain_text_token'] = $this->plainTextToken;
        }

        if ($this->createdAt !== null) {
            $data['created_at'] = $this->createdAt;
        }

        return $data;
    }
}
