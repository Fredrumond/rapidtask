<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Projeto',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nome', type: 'string', example: 'Portal do Cliente'),
        new OA\Property(property: 'sigla', type: 'string', example: 'PDC', maxLength: 20),
        new OA\Property(property: 'descricao', type: 'string', nullable: true),
        new OA\Property(property: 'dt_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_prevista', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_fim', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'cliente', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'time', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ProjetoRequest',
    required: ['time_id', 'nome', 'sigla', 'cliente_id'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional (obrigatório no body das mutações)'),
        new OA\Property(property: 'nome', type: 'string', maxLength: 255),
        new OA\Property(property: 'sigla', type: 'string', maxLength: 20),
        new OA\Property(property: 'cliente_id', type: 'integer'),
        new OA\Property(property: 'descricao', type: 'string', nullable: true),
        new OA\Property(property: 'dt_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_prevista', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_fim', type: 'string', format: 'date', nullable: true),
    ],
    type: 'object',
)]
class ProjetoSchemas {}
