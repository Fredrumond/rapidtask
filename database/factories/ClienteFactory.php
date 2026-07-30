<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Time;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'email' => fake()->optional()->companyEmail(),
            'telefone' => fake()->optional()->phoneNumber(),
            'usuario_id' => User::factory(),
            'time_id' => Time::factory(),
        ];
    }

    /**
     * Indicate that the client has no contact details.
     */
    public function semContato(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'telefone' => null,
        ]);
    }
}
