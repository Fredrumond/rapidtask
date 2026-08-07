<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NestedLookup',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nome', type: 'string', example: 'Novo'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'NestedUsuario',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Maria Silva'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'Tarefa',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Implementar API'),
        new OA\Property(property: 'descricao', type: 'string', nullable: true),
        new OA\Property(property: 'dt_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_prevista', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_fim', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'tempo_estimado', type: 'integer', nullable: true, example: 120),
        new OA\Property(property: 'status', type: 'integer', example: 0),
        new OA\Property(property: 'tipo', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'situacao', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'prioridade', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'projeto', ref: '#/components/schemas/NestedLookup'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TarefaRequest',
    required: ['time_id', 'titulo', 'projeto_id', 'tipo_id', 'situacao_id', 'prioridade_id'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional (obrigatório no body das mutações)'),
        new OA\Property(property: 'titulo', type: 'string', maxLength: 255),
        new OA\Property(property: 'descricao', type: 'string', nullable: true),
        new OA\Property(property: 'projeto_id', type: 'integer'),
        new OA\Property(property: 'tipo_id', type: 'integer'),
        new OA\Property(property: 'situacao_id', type: 'integer'),
        new OA\Property(property: 'prioridade_id', type: 'integer'),
        new OA\Property(property: 'dt_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_prevista', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'dt_fim', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'tempo_estimado', type: 'integer', nullable: true),
        new OA\Property(property: 'status', type: 'integer', nullable: true, description: 'Apenas no PUT'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ApiMessageResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'object'),
    ],
    type: 'object',
)]
class TarefaSchemas {}
