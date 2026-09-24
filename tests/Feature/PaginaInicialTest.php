<?php

use App\Exceptions\DuvidaException;
use App\Models\Duvida;
use App\Services\DuvidaService;
use Livewire\Volt\Volt;

test('a pagina inicial responde sem autenticacao e aponta o call to action para o cadastro', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Apresentação', false)
        ->assertSee('Pontos da ferramenta', false)
        ->assertSee('Depoimentos', false)
        ->assertSee('Dúvidas', false)
        ->assertSee('href="'.route('register').'"', false);
});

test('o carrossel exibe os quatro depoimentos ao percorrer o componente', function () {
    $depoimentos = [
        ['Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.', 'Marina Costa'],
        ['Tarefas, comentários e clientes no mesmo lugar tiraram a bagunça da nossa rotina.', 'Paulo Henrique'],
        ['O painel me mostra o que precisa de atenção antes da reunião começar.', 'Aline Ferreira'],
        ['Conseguimos acompanhar o trabalho pela tela e pela API, sem planilha paralela.', 'Ricardo Nunes'],
    ];

    $component = Volt::test('pages.home');

    foreach ($depoimentos as [$texto, $autor]) {
        $component->assertSee($texto)->assertSee($autor);
        $component->call('avancarDepoimento');
    }
});

test('a troca de bloco exibe cada um dos quatro pontos da ferramenta', function () {
    $blocos = [
        'Acesse o RapidTask com a sua conta.',
        'Gerencie tarefas, projetos, clientes e comentários na interface web e na API.',
        'Crie e controle os tokens de acesso da conta.',
        'Acompanhe no painel o que precisa de atenção.',
    ];

    $component = Volt::test('pages.home');

    foreach ($blocos as $indice => $texto) {
        $component->call('selecionarBloco', $indice)->assertSee($texto);
    }
});

test('envio valido grava a duvida sem autenticacao', function () {
    Volt::test('pages.home')
        ->set('nome', 'Marina Costa')
        ->set('email', 'marina@exemplo.com')
        ->set('telefone', '11988887777')
        ->set('duvida', 'Como acompanho um projeto?')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviado', true)
        ->assertSee('Dúvida enviada. Obrigado pelo contato.');

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'Marina Costa',
        'email' => 'marina@exemplo.com',
        'telefone' => '11988887777',
        'duvida' => 'Como acompanho um projeto?',
    ]);

    expect(Duvida::query()->count())->toBe(1)
        ->and(auth()->check())->toBeFalse();
});

test('campo obrigatorio vazio ou email invalido nao grava e retorna erro de validacao', function (array $dados, string $campo) {
    Volt::test('pages.home')
        ->set('nome', $dados['nome'])
        ->set('email', $dados['email'])
        ->set('telefone', $dados['telefone'])
        ->set('duvida', $dados['duvida'])
        ->call('enviar')
        ->assertHasErrors([$campo])
        ->assertSet('enviado', false)
        ->assertDontSee('Dúvida enviada. Obrigado pelo contato.');

    expect(Duvida::query()->count())->toBe(0);
})->with([
    'nome vazio' => [
        ['nome' => '', 'email' => 'marina@exemplo.com', 'telefone' => '11988887777', 'duvida' => 'Como acompanho um projeto?'],
        'nome',
    ],
    'email vazio' => [
        ['nome' => 'Marina Costa', 'email' => '', 'telefone' => '11988887777', 'duvida' => 'Como acompanho um projeto?'],
        'email',
    ],
    'telefone vazio' => [
        ['nome' => 'Marina Costa', 'email' => 'marina@exemplo.com', 'telefone' => '', 'duvida' => 'Como acompanho um projeto?'],
        'telefone',
    ],
    'duvida vazia' => [
        ['nome' => 'Marina Costa', 'email' => 'marina@exemplo.com', 'telefone' => '11988887777', 'duvida' => ''],
        'duvida',
    ],
    'email invalido' => [
        ['nome' => 'Marina Costa', 'email' => 'nao-e-email', 'telefone' => '11988887777', 'duvida' => 'Como acompanho um projeto?'],
        'email',
    ],
]);

test('falha ao gravar a duvida nao confirma o envio', function () {
    $this->mock(DuvidaService::class, function ($mock): void {
        $mock->shouldReceive('create')->once()->andThrow(DuvidaException::createFailed());
    });

    Volt::test('pages.home')
        ->set('nome', 'Marina Costa')
        ->set('email', 'marina@exemplo.com')
        ->set('telefone', '11988887777')
        ->set('duvida', 'Como acompanho um projeto?')
        ->call('enviar')
        ->assertHasErrors(['formulario'])
        ->assertSet('enviado', false)
        ->assertDontSee('Dúvida enviada. Obrigado pelo contato.')
        ->assertSee('Não foi possível registrar a dúvida.');

    expect(Duvida::query()->count())->toBe(0);
});
