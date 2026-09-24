<?php

namespace Database\Factories;

use App\Models\Duvida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Duvida>
 */
class DuvidaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->safeEmail(),
            'telefone' => fake()->numerify('119########'),
            'duvida' => fake()->sentence(),
        ];
    }
}
