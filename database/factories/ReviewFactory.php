<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'product_id' => \App\Models\Product::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'image_url' => $this->faker->imageUrl(640, 480, 'nature'),
            'comment' => $this->faker->sentence(),
            'review_date' => now(),
            'created_at' => now(),
        ];
    }
}
