<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TarefaComentario',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'tarefa_id', type: 'integer', example: 10),
        new OA\Property(property: 'comentario', type: 'string', example: 'Comentário de progresso'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TarefaComentarioRequest',
    required: ['time_id', 'comentario'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional'),
        new OA\Property(property: 'comentario', type: 'string'),
    ],
    type: 'object',
)]
class TarefaComentarioSchemas {}
