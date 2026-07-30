<?php

namespace Database\Factories;

use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeMembro>
 */
class TimeMembroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'time_id' => Time::factory(),
            'usuario_id' => User::factory(),
            'nivel_id' => 2,
        ];
    }

    /**
     * Indicate that the member is an admin (Nivel I).
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel_id' => 1,
        ]);
    }

    /**
     * Indicate that the member is a regular member (Nivel II).
     */
    public function membro(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel_id' => 2,
        ]);
    }
}
