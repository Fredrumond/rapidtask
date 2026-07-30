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
                        ['titulo' => 'Editar time', 'estado' => 'development'],
                        ['titulo' => 'Excluir time', 'estado' => 'development', 'nota' => 'Cascata de exclusão ainda em revisão'],
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
                        ['titulo' => 'Excluir projeto', 'estado' => 'development'],
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
