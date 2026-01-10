<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'username' => $this->faker->unique()->userName(),
            'age' => $this->faker->numberBetween(1, 100),
            'reputation_score' => 0,
            'version' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Keep this for compatibility with Laravel traits/tests
     * even if you don’t currently use it.
     */
    public function unverified(): static
    {
        return $this;
    }
}
