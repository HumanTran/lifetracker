<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),

            'role' => 'user',
            'status' => fake()->randomElement(['active', 'active', 'active', 'banned']),

            'last_login_at' => fake()->dateTimeBetween($createdAt, 'now'),
            'last_login_ip' => fake()->ipv4(),

            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
