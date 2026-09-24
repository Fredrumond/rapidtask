<?php

use App\Models\Duvida;
use App\Models\User;
use Livewire\Volt\Volt;

test('a página inicial apresenta o RapidTask e leva à criação de conta', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Acompanhe projetos, tarefas e clientes no mesmo lugar.')
        ->assertSee('href="'.route('register').'"', false)
        ->assertSee('Autenticação')
        ->assertSee('Tarefas, projetos, clientes e comentários')
        ->assertSee('Gerenciamento de tokens')
        ->assertSee('Dashboard')
        ->assertSee('Passei a enxergar o andamento dos projetos sem cobrar atualização o tempo todo.')
        ->assertSee('Marina Costa')
        ->assertSee('Paulo Henrique')
        ->assertSee('Aline Ferreira')
        ->assertSee('Ricardo Nunes')
        ->assertSee('id="apresentacao"', false)
        ->assertSee('id="pontos"', false)
        ->assertSee('id="depoimentos"', false)
        ->assertSee('id="duvidas"', false)
        ->assertSee('Nome')
        ->assertSee('E-mail')
        ->assertSee('Telefone')
        ->assertSee('Dúvida');
});

test('visitante autenticado vê o caminho para o painel', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('dashboard').'"', false);
});

test('o formulário do rodapé registra a dúvida', function (): void {
    Volt::test('pages.inicio')
        ->set('nome', '  Marina Costa  ')
        ->set('email', 'Marina@Example.com')
        ->set('telefone', '11988887777')
        ->set('duvida', 'Como crio uma conta?')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviada', true)
        ->assertSet('nome', '')
        ->assertSee('Dúvida registrada.');

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'Marina Costa',
        'email' => 'marina@example.com',
        'telefone' => '11988887777',
        'duvida' => 'Como crio uma conta?',
    ]);
});

test('o formulário do rodapé exige os campos', function (): void {
    Volt::test('pages.inicio')
        ->set('nome', '')
        ->set('email', '')
        ->set('telefone', '')
        ->set('duvida', '')
        ->call('enviar')
        ->assertHasErrors(['nome', 'email', 'telefone', 'duvida']);

    expect(Duvida::query()->exists())->toBeFalse();
});

test('o formulário rejeita texto em branco pela regra de domínio', function (): void {
    Volt::test('pages.inicio')
        ->set('nome', '   ')
        ->set('email', 'marina@example.com')
        ->set('telefone', '11988887777')
        ->set('duvida', 'Como crio uma conta?')
        ->call('enviar')
        ->assertHasErrors(['nome']);

    expect(Duvida::query()->exists())->toBeFalse();
});
