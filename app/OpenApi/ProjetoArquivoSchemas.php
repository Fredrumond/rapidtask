<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProjetoArquivo',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'projeto_id', type: 'integer', example: 10),
        new OA\Property(property: 'nome', type: 'string', example: 'Contrato assinado'),
        new OA\Property(property: 'descricao', type: 'string', example: 'Contrato do cliente em PDF'),
        new OA\Property(property: 'usuario', ref: '#/components/schemas/NestedUsuario'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ProjetoArquivoRequest',
    required: ['time_id', 'nome', 'descricao', 'arquivo'],
    properties: [
        new OA\Property(property: 'time_id', type: 'integer', description: 'ID do time operacional'),
        new OA\Property(property: 'nome', type: 'string', maxLength: 255),
        new OA\Property(property: 'descricao', type: 'string'),
        new OA\Property(
            property: 'arquivo',
            description: 'Arquivo (pdf, png, jpg, jpeg, doc, docx, xls, xlsx, txt; máx. 10 MB)',
            type: 'string',
            format: 'binary',
        ),
    ],
    type: 'object',
)]
class ProjetoArquivoSchemas {}
