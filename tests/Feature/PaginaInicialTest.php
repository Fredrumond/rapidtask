<?php

use App\Models\Duvida;
use Livewire\Volt\Volt;

test('pagina inicial apresenta as secoes e o call to action leva ao cadastro', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('RapidTask')
        ->assertSee('O trabalho do time, em um só lugar.')
        ->assertSee('Experimentar')
        ->assertSee(route('register'), false)
        ->assertSee('O que o RapidTask oferece')
        ->assertSee('Quem já usa')
        ->assertSee('Dúvidas')
        ->assertSee('Nome')
        ->assertSee('E-mail')
        ->assertSee('Telefone')
        ->assertDontSee('7 dias')
        ->assertDontSee('pagamento');
});

test('carrossel percorre os quatro depoimentos', function () {
    $component = Volt::test('pages.inicio');

    $component
        ->assertSee('Marina Costa')
        ->assertDontSee('Paulo Henrique')
        ->assertDontSee('Aline Ferreira')
        ->assertDontSee('Ricardo Nunes');

    $component->call('proximoDepoimento')
        ->assertSee('Paulo Henrique')
        ->assertDontSee('Marina Costa');

    $component->call('proximoDepoimento')
        ->assertSee('Aline Ferreira');

    $component->call('proximoDepoimento')
        ->assertSee('Ricardo Nunes');

    $component->call('proximoDepoimento')
        ->assertSee('Marina Costa');

    $component->call('depoimentoAnterior')
        ->assertSee('Ricardo Nunes');

    $component->call('selecionarDepoimento', 1)
        ->assertSee('Paulo Henrique')
        ->assertSee('Tarefas, comentários e clientes no mesmo lugar tiraram a bagunça da nossa rotina.');
});

test('secao de pontos muda entre os blocos', function () {
    $component = Volt::test('pages.inicio');

    $component
        ->assertSee('Autenticação')
        ->assertSee('Entre com a sua conta para acessar o RapidTask.')
        ->assertDontSee('Gere e revogue tokens');

    $component->call('selecionarPonto', 1)
        ->assertSee('na interface web e na API')
        ->assertDontSee('Entre com a sua conta para acessar o RapidTask.');

    $component->call('selecionarPonto', 2)
        ->assertSee('Gere e revogue tokens de API da conta');

    $component->call('selecionarPonto', 3)
        ->assertSee('precisam de atenção antes da reunião começar');
});

test('duvida enviada no rodape fica registrada', function () {
    Volt::test('pages.inicio')
        ->set('nome', ' Ana Lima ')
        ->set('email', 'ana@example.com')
        ->set('telefone', '11999998888')
        ->set('duvida', 'Como convido o time?')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviada', true)
        ->assertSet('nome', '');

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'Ana Lima',
        'email' => 'ana@example.com',
        'telefone' => '11999998888',
        'duvida' => 'Como convido o time?',
    ]);
});

test('duvida sem dados obrigatorios nao e registrada', function () {
    Volt::test('pages.inicio')
        ->set('nome', '')
        ->set('email', 'nao-e-email')
        ->set('telefone', '')
        ->set('duvida', '')
        ->call('enviar')
        ->assertHasErrors(['nome', 'email', 'telefone', 'duvida']);

    expect(Duvida::query()->count())->toBe(0);
});
