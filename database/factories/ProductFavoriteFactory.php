<?php

namespace Database\Factories;

use App\Models\ProductFavorite;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFavoriteFactory extends Factory
{
    protected $model = ProductFavorite::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(),
        ];
    }
}
