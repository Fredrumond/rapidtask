<?php

namespace Database\Factories;

use App\Models\Time;
use App\Models\TimeMembroConvite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TimeMembroConvite>
 */
class TimeMembroConviteFactory extends Factory
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
            'email' => fake()->unique()->safeEmail(),
            'time_id' => Time::factory(),
            'token' => Str::random(32),
            'status' => 0,
        ];
    }

    /**
     * Indicate that the invite is pending.
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    /**
     * Indicate that the invite was accepted.
     */
    public function aceito(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the invite was declined.
     */
    public function recusado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }
}
