<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'image_url' => $this->faker->imageUrl(640, 480, 'nature'),
            'created_at' => now(),
        ];
    }
}
