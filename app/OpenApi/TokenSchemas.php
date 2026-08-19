<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Token',
    properties: [
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'name', type: 'string', example: 'api'),
        new OA\Property(
            property: 'plain_text_token',
            type: 'string',
            nullable: true,
            description: 'Presente somente na emissão. Não é persistido nem reexibido.',
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
class TokenSchemas {}
