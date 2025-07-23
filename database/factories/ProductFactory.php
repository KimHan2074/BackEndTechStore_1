<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'promotion_type' => $this->faker->randomElement(['hot', 'best deal', 'new', 'summer sale', 'featured product']),
            'old_price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
            'category_id' => \App\Models\Category::factory(),
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['sold out', 'null']),
            'start_date' => now(),
            'end_date' => now(),
            'created_at' => now(),
        ];
    }
}
