<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // auto create user
            'type' => 'email',
            'title' => $this->faker->sentence(),
            'message' => $this->faker->sentence(),
            'payload' => null,
            'status' => 'pending',
        ];
    }
}
