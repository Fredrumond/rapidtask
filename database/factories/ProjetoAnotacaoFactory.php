<?php

namespace Database\Factories;

use App\Models\Projeto;
use App\Models\ProjetoAnotacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjetoAnotacao>
 */
class ProjetoAnotacaoFactory extends Factory
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
            'anotacao' => fake()->paragraph(),
        ];
    }
}
