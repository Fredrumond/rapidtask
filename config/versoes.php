<?php

/*
|--------------------------------------------------------------------------
| Histórico de versões
|--------------------------------------------------------------------------
|
| Releases em ordem decrescente: o primeiro item é a versão atual.
| Os estados vêm da legenda do changelog original (Laravel 5.7) e indicam
| a maturidade de cada entrega, não o status do release inteiro.
|
| Não use classes CSS aqui: o Tailwind só varre resources/views.
|
*/

return [

    'estados' => [
        'stable' => 'Estável',
        'development' => 'Development',
        'test' => 'Test',
        'bug' => 'Bug',
    ],

    'releases' => [

        [
            'versao' => '1.0.0-alpha.4',
            'data' => '2026-08-06',
            'estado' => 'development',
            'titulo' => 'Conta SaaS e API por tenant',
            'resumo' => 'Conta como tenant externo acima dos times, CRUD de tarefas via API alinhado à conta, e token Sanctum gerenciado nas configurações da conta (owner).',
            'nota' => 'Cutover direto: tokens pessoais por usuário deixam de valer. Escopos granulares, múltiplos tokens e multi-conta por usuário ficam fora desta entrega.',
            'modulos' => [
                [
                    'nome' => 'Conta SaaS',
                    'itens' => [
                        ['titulo' => 'Modelo conta + time.conta_id', 'estado' => 'stable', 'nota' => 'Backfill de times existentes'],
                        ['titulo' => 'Registro cria conta do owner', 'estado' => 'stable'],
                        ['titulo' => 'Sessão com current_conta_id', 'estado' => 'stable'],
                        ['titulo' => 'Editar nome da conta', 'estado' => 'stable', 'nota' => 'Apenas owner'],
                        ['titulo' => 'Convite bloqueia e-mail de outra conta', 'estado' => 'stable'],
                        ['titulo' => 'Isolamento cross-conta (scopes + policies)', 'estado' => 'stable'],
                        ['titulo' => 'Nav com nome da conta', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'API',
                    'itens' => [
                        ['titulo' => 'Token por Conta (Sanctum)', 'estado' => 'stable', 'nota' => 'Um ativo por conta; gerar novo revoga o anterior'],
                        ['titulo' => 'Gerar/revogar token (POST/DELETE /api/tokens)', 'estado' => 'stable', 'nota' => 'Bearer da Conta'],
                        ['titulo' => 'CRUD /api/tarefas', 'estado' => 'stable', 'nota' => 'time_id na query (GET) e no body (POST/PUT/DELETE)'],
                        ['titulo' => 'Middleware api.team', 'estado' => 'stable', 'nota' => 'Valida time pertencente à conta autenticada'],
                        ['titulo' => 'Isolamento cross-conta na API', 'estado' => 'stable'],
                        ['titulo' => 'Swagger / OpenAPI', 'estado' => 'stable', 'nota' => 'UI em /api/documentation'],
                    ],
                ],
                [
                    'nome' => 'Configuração da conta',
                    'itens' => [
                        ['titulo' => 'Gerenciar token de API', 'estado' => 'stable', 'nota' => 'Plain text só na emissão; owner apenas'],
                    ],
                ],
                [
                    'nome' => 'Meu Perfil',
                    'itens' => [
                        ['titulo' => 'Token de API removido do perfil', 'estado' => 'stable', 'nota' => 'Migrado para configurações da conta'],
                    ],
                ],
                [
                    'nome' => 'Ajustes',
                    'itens' => [
                        ['titulo' => 'Fim do header X-Time-Id', 'estado' => 'stable', 'nota' => 'ADR 0003 supersede ADR 0001'],
                        ['titulo' => 'Editar time marcado estável', 'estado' => 'stable'],
                        ['titulo' => 'Excluir projeto marcado estável', 'estado' => 'stable'],
                    ],
                ],
            ],
        ],

        [
            'versao' => '1.0.0-alpha.3',
            'data' => '2026-08-03',
            'estado' => 'development',
            'titulo' => 'Autenticação da API',
            'resumo' => 'Primeira camada de API com Laravel Sanctum: geração e revogação de token pessoal (um por usuário), endpoints Bearer e gestão pelo perfil no painel web.',
            'nota' => 'Superseded em parte pela 1.0.0-alpha.4 (token passa a ser da Conta). Token vitalício até revogação explícita ou geração de um novo.',
            'modulos' => [
                [
                    'nome' => 'API',
                    'itens' => [
                        ['titulo' => 'Laravel Sanctum', 'estado' => 'stable'],
                        ['titulo' => 'Gerar token (POST /api/tokens)', 'estado' => 'stable', 'nota' => 'Modelo inicial por usuário; ver alpha.4'],
                        ['titulo' => 'Revogar token (DELETE /api/tokens)', 'estado' => 'stable'],
                        ['titulo' => 'Middleware auth:sanctum', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Meu Perfil',
                    'itens' => [
                        ['titulo' => 'Gerenciar token de API', 'estado' => 'stable', 'nota' => 'Removido na alpha.4; gestão na conta'],
                    ],
                ],
            ],
        ],

        [
            'versao' => '1.0.0-alpha.2',
            'data' => '2026-07-30',
            'estado' => 'development',
            'titulo' => 'Refatoração para Laravel 13',
            'resumo' => 'Reescrita da base sobre Laravel 13 e PHP 8.4, com isolamento multi-tenant por time, painel em Livewire Volt e as 15 issues de segurança do baseline corrigidas.',
            'nota' => 'O código Laravel 5.7 das versões anteriores segue na branch versao_pre_refatoracao.',
            'modulos' => [
                [
                    'nome' => 'Plataforma',
                    'itens' => [
                        ['titulo' => 'Laravel 13 + PHP 8.4', 'estado' => 'stable'],
                        ['titulo' => 'Livewire 3 + Volt', 'estado' => 'stable'],
                        ['titulo' => 'Tailwind + Vite', 'estado' => 'stable'],
                        ['titulo' => 'Docker (nginx, MySQL 8.4, queue)', 'estado' => 'stable'],
                        ['titulo' => 'CI com Pest e Pint', 'estado' => 'stable'],
                        ['titulo' => 'Traduções pt_BR', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Segurança',
                    'itens' => [
                        ['titulo' => 'Isolamento multi-tenant por time', 'estado' => 'stable', 'nota' => 'Global scopes + policies em todos os recursos'],
                        ['titulo' => 'Correção de IDOR entre times', 'estado' => 'stable'],
                        ['titulo' => 'Correção de SQL injection na busca', 'estado' => 'stable'],
                        ['titulo' => 'Exclusões via POST/DELETE', 'estado' => 'stable'],
                        ['titulo' => 'Convites com URL assinada', 'estado' => 'stable'],
                        ['titulo' => 'Download de arquivo autorizado', 'estado' => 'stable'],
                        ['titulo' => 'Uploads validados por Form Request', 'estado' => 'stable'],
                        ['titulo' => 'Remoção da rota de debug de e-mail', 'estado' => 'stable'],
                        ['titulo' => 'Senha não trafega mais por e-mail', 'estado' => 'stable'],
                        ['titulo' => 'Logs deixam de ser públicos', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Autenticação',
                    'itens' => [
                        ['titulo' => 'Login e registro (Breeze Livewire)', 'estado' => 'stable'],
                        ['titulo' => 'Verificação de e-mail', 'estado' => 'stable'],
                        ['titulo' => 'Recuperação de senha', 'estado' => 'stable'],
                        ['titulo' => 'Confirmação de senha', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Times',
                    'itens' => [
                        ['titulo' => 'Seletor de time ativo', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar time e membros', 'estado' => 'stable'],
                        ['titulo' => 'Convidar membro (e-mail na fila)', 'estado' => 'stable'],
                        ['titulo' => 'Editar time', 'estado' => 'stable'],
                        ['titulo' => 'Excluir time', 'estado' => 'stable', 'nota' => 'Soft delete; apenas admin do time'],
                    ],
                ],
                [
                    'nome' => 'Clientes',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Listar com busca', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable', 'nota' => 'Bug de permissão do legado corrigido'],
                        ['titulo' => 'Excluir', 'estado' => 'stable', 'nota' => 'Bug de permissão do legado corrigido'],
                    ],
                ],
                [
                    'nome' => 'Projetos',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Listar com busca', 'estado' => 'stable'],
                        ['titulo' => 'Quadro por situação', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                        ['titulo' => 'Arquivos', 'estado' => 'development', 'nota' => 'Model, policy e download prontos; tela pendente'],
                        ['titulo' => 'Anotações', 'estado' => 'development', 'nota' => 'Model e policy prontos; tela pendente'],
                        ['titulo' => 'Histórico', 'estado' => 'development'],
                        ['titulo' => 'Excluir projeto', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Tarefas',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Listar com busca', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                        ['titulo' => 'Arquivar', 'estado' => 'stable'],
                        ['titulo' => 'Arquivadas por time', 'estado' => 'stable', 'nota' => 'Antes a listagem era global'],
                        ['titulo' => 'Comentários', 'estado' => 'development', 'nota' => 'Model e policy prontos; tela pendente'],
                        ['titulo' => 'Histórico', 'estado' => 'development'],
                    ],
                ],
                [
                    'nome' => 'Meu Perfil',
                    'itens' => [
                        ['titulo' => 'Editar nome e e-mail', 'estado' => 'stable', 'nota' => 'Não é mais possível editar perfil alheio'],
                        ['titulo' => 'Alterar senha', 'estado' => 'stable'],
                        ['titulo' => 'Excluir conta', 'estado' => 'stable'],
                        ['titulo' => 'Avatar', 'estado' => 'development'],
                    ],
                ],
            ],
        ],

        [
            'versao' => '1.0.0-alpha.1',
            'data' => null,
            'data_label' => '2020',
            'estado' => 'test',
            'titulo' => 'Times, arquivos e anotações',
            'resumo' => 'Última entrega da base Laravel 5.7: gestão de times com convites, arquivos e anotações em projetos, e ajustes de comentários de tarefa.',
            'nota' => 'Importado do changelog legado, que registrava a data como 00/00/2020.',
            'modulos' => [
                [
                    'nome' => 'Tarefas',
                    'itens' => [
                        ['titulo' => 'Editar comentário', 'estado' => 'stable'],
                        ['titulo' => 'Excluir comentário', 'estado' => 'stable'],
                        ['titulo' => 'Restaurar tarefa arquivada', 'estado' => 'stable'],
                        ['titulo' => 'Excluir tarefa arquivada', 'estado' => 'bug'],
                    ],
                ],
                [
                    'nome' => 'Projetos',
                    'itens' => [
                        ['titulo' => 'Visualizar', 'estado' => 'stable'],
                        ['titulo' => 'Adicionar arquivos', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar arquivos', 'estado' => 'stable'],
                        ['titulo' => 'Excluir arquivos', 'estado' => 'bug'],
                        ['titulo' => 'Adicionar anotações', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar anotações', 'estado' => 'stable'],
                        ['titulo' => 'Editar anotações', 'estado' => 'stable'],
                        ['titulo' => 'Excluir anotações', 'estado' => 'stable'],
                        ['titulo' => 'Histórico', 'estado' => 'development'],
                        ['titulo' => 'Excluir projeto', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Clientes',
                    'itens' => [
                        ['titulo' => 'Editar', 'estado' => 'bug'],
                        ['titulo' => 'Excluir', 'estado' => 'bug'],
                    ],
                ],
                [
                    'nome' => 'Times',
                    'itens' => [
                        ['titulo' => 'Criar', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                        ['titulo' => 'Excluir', 'estado' => 'stable'],
                        ['titulo' => 'Convidar membro para o time', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Meu Perfil',
                    'itens' => [
                        ['titulo' => 'Nome', 'estado' => 'bug'],
                        ['titulo' => 'Avatar', 'estado' => 'bug'],
                        ['titulo' => 'Senha', 'estado' => 'bug'],
                    ],
                ],
            ],
        ],

        [
            'versao' => '1.0.0-alpha.0',
            'data' => '2019-04-02',
            'estado' => 'stable',
            'titulo' => 'Primeira versão',
            'resumo' => 'CRUD inicial de clientes, projetos e tarefas, com comentários e histórico de tarefa.',
            'nota' => 'Importado do changelog legado. Corresponde à tag v1.0.0-alpha.0.',
            'modulos' => [
                [
                    'nome' => 'Tarefas',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                        ['titulo' => 'Excluir', 'estado' => 'stable'],
                        ['titulo' => 'Arquivar', 'estado' => 'stable'],
                        ['titulo' => 'Comentário de tarefa', 'estado' => 'stable'],
                        ['titulo' => 'Histórico de tarefa', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Projetos',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar', 'estado' => 'development'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                    ],
                ],
                [
                    'nome' => 'Clientes',
                    'itens' => [
                        ['titulo' => 'Cadastrar', 'estado' => 'stable'],
                        ['titulo' => 'Visualizar', 'estado' => 'stable'],
                        ['titulo' => 'Editar', 'estado' => 'stable'],
                        ['titulo' => 'Excluir', 'estado' => 'stable'],
                    ],
                ],
            ],
        ],

    ],

];
