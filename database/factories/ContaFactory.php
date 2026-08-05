<?php

namespace Database\Factories;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conta>
 */
class ContaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => 'Conta de '.fake()->name(),
            'usuario_id' => User::factory(),
        ];
    }

    /**
     * Conta com nome explícito.
     */
    public function comNome(string $nome): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $nome,
        ]);
    }
}
