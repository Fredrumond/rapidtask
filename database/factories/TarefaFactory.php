<?php

namespace Database\Factories;

use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tarefa>
 */
class TarefaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_id' => 1,
            'titulo' => fake()->sentence(4),
            'descricao' => fake()->optional()->paragraph(),
            'situacao_id' => 1,
            'prioridade_id' => 2,
            'dt_inicio' => fake()->optional()->date(),
            'dt_prevista' => fake()->optional()->dateTimeBetween('now', '+3 months'),
            'dt_fim' => null,
            'tempo_estimado' => fake()->optional()->numberBetween(1, 480),
            'status' => 0,
            'projeto_id' => Projeto::factory(),
            'usuario_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the task is in progress.
     */
    public function emAndamento(): static
    {
        return $this->state(fn (array $attributes) => [
            'situacao_id' => 2,
        ]);
    }

    /**
     * Indicate that the task is finished.
     */
    public function finalizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'situacao_id' => 4,
            'dt_fim' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the task is archived.
     */
    public function arquivada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the task is urgent.
     */
    public function urgente(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridade_id' => 3,
        ]);
    }
}
