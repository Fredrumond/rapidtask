<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Time;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Projeto>
 */
class ProjetoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-6 months', 'now');
        $prevista = fake()->dateTimeBetween($inicio, '+6 months');

        return [
            'nome' => fake()->words(3, true),
            'descricao' => fake()->optional()->paragraph(),
            'sigla' => strtoupper(fake()->unique()->lexify('???')),
            'cliente_id' => Cliente::factory(),
            'usuario_id' => User::factory(),
            'time_id' => Time::factory(),
            'dt_inicio' => $inicio,
            'dt_prevista' => $prevista,
            'dt_fim' => null,
        ];
    }

    /**
     * Indicate that the project is finished.
     */
    public function finalizado(): static
    {
        return $this->state(function (array $attributes) {
            $inicio = fake()->dateTimeBetween('-1 year', '-3 months');
            $fim = fake()->dateTimeBetween($inicio, '-1 month');

            return [
                'dt_inicio' => $inicio,
                'dt_prevista' => fake()->dateTimeBetween($inicio, $fim),
                'dt_fim' => $fim,
            ];
        });
    }
}
