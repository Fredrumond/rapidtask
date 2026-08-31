<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'RapidTask API',
    description: 'API REST do RapidTask para integração com sistemas externos.',
)]
#[OA\Server(url: '/api', description: 'API base path')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    description: 'Bearer token Sanctum da Conta (Authorization: Bearer {token})',
    scheme: 'bearer',
    bearerFormat: 'JWT',
)]
#[OA\Tag(name: 'Clientes', description: 'CRUD de clientes')]
#[OA\Tag(name: 'Projetos', description: 'CRUD de projetos')]
#[OA\Tag(name: 'Tarefas', description: 'CRUD de tarefas')]
#[OA\Tag(name: 'Tokens', description: 'Emissão e revogação de tokens de API')]
class OpenApiSpec {}
