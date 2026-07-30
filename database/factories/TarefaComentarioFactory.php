<?php

namespace Database\Factories;

use App\Models\Tarefa;
use App\Models\TarefaComentario;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TarefaComentario>
 */
class TarefaComentarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tarefa_id' => Tarefa::factory(),
            'usuario_id' => User::factory(),
            'comentario' => fake()->paragraph(),
        ];
    }
}
