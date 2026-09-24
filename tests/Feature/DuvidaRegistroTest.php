<?php

use App\Exceptions\DuvidaDomainException;
use App\Models\Duvida;
use App\Services\DuvidaService;

test('registrar duvida persiste os campos', function (): void {
    app(DuvidaService::class)->registrar([
        'nome' => 'Ana Lima',
        'email' => 'ana@example.com',
        'telefone' => '11988887777',
        'mensagem' => 'Como acompanho um projeto?',
    ]);

    $this->assertDatabaseHas('duvidas', [
        'nome' => 'Ana Lima',
        'email' => 'ana@example.com',
        'telefone' => '11988887777',
        'mensagem' => 'Como acompanho um projeto?',
    ]);
});

test('registrar duvida em branco falha no dominio e nao persiste', function (): void {
    expect(fn () => app(DuvidaService::class)->registrar([
        'nome' => '   ',
        'email' => 'ana@example.com',
        'telefone' => '11988887777',
        'mensagem' => 'Dúvida',
    ]))->toThrow(DuvidaDomainException::class);

    expect(Duvida::query()->count())->toBe(0);
});
