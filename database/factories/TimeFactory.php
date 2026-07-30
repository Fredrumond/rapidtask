<?php

namespace Database\Factories;

use App\Models\Time;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Time>
 */
class TimeFactory extends Factory
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
            'logo' => null,
            'usuario_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the team has a logo.
     */
    public function withLogo(): static
    {
        return $this->state(fn (array $attributes) => [
            'logo' => fake()->uuid().'.png',
        ]);
    }
}
