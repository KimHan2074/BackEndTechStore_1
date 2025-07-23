<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
class UserFactory extends Factory
{
    private static $password;
        public function definition()
        {
            return [
                'name' => $this->faker->name(), 
                'email' => $this->faker->unique()->safeEmail(), 
                'password' => static::$password ??= Hash::make('password'), 
                'email_otp' => null, 
                'email_verified' => $this->faker->boolean(), 
                'role' => $this->faker->randomElement(['user', 'admin']), 
                'address' => $this->faker->address(), 
                'phone' => $this->faker->phoneNumber(), 
                'avatar' => $this->faker->imageUrl(640, 480, 'nature'), 
                'created_at' => now(), 
                'updated_at' => now(), 
            ];
        }
}

