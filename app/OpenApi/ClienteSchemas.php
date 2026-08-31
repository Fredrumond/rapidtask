<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Cliente',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nome', type: 'string', example: 'Acme Ltda'),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true, example: 'contato@acme.test'),
        new OA\Property(property: 'telefone', type: 'string', nullable: true, example: '11999999999'),
        new OA\Property(property: 'time', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ClienteRequest',
    required: ['time_id', 'nome'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional (obrigatório no body das mutações)'),
        new OA\Property(property: 'nome', type: 'string', maxLength: 255),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
        new OA\Property(property: 'telefone', type: 'string', nullable: true, maxLength: 255),
    ],
    type: 'object',
)]
class ClienteSchemas {}
