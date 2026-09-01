<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProjetoAnotacao',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'projeto_id', type: 'integer', example: 10),
        new OA\Property(property: 'anotacao', type: 'string', example: 'Reunião de kickoff agendada'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ProjetoAnotacaoRequest',
    required: ['time_id', 'anotacao'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional'),
        new OA\Property(property: 'anotacao', type: 'string'),
    ],
    type: 'object',
)]
class ProjetoAnotacaoSchemas {}
