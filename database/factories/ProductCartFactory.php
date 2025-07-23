<?php

namespace Database\Factories;

use App\Models\ProductCart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCartFactory extends Factory
{
    protected $model = ProductCart::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
