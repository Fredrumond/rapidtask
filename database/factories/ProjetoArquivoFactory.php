<?php

namespace Database\Factories;

use App\Models\Projeto;
use App\Models\ProjetoArquivo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjetoArquivo>
 */
class ProjetoArquivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'projeto_id' => Projeto::factory(),
            'usuario_id' => User::factory(),
            'nome' => fake()->words(3, true),
            'descricao' => fake()->sentence(),
            'src' => 'projetos/'.fake()->uuid().'.pdf',
        ];
    }
}
