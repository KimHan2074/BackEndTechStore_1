<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductDescriptionFactory extends Factory
{
    protected $model = \App\Models\ProductDescription::class;

    public function definition()
    {
        return [
            'product_id'    => Product::factory(),
            'name'          => $this->faker->name,
            'description'   => $this->faker->paragraph,
            'features'      => $this->faker->text(200),
        ];
    }
}
