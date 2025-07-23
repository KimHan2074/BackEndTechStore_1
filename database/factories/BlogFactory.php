<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    public function definition()
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'image_url' => $this->faker->imageUrl(640, 480, 'nature'),
            'link_url' => $this->faker->url(),
            'status' => $this->faker->randomElement(['Lastest New', 'null']),
            'author_id' => \App\Models\User::factory(),
            'publish_date' => now(),
            'created_at' => now(),
        ];
    }
}
