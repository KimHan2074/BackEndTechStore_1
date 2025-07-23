<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductSpecificationFactory extends Factory
{
    protected $model = \App\Models\ProductSpecification::class;

    public function definition()
    {
        return [
            'product_id'    => Product::factory(),
            'brand'         => $this->faker->company,
            'model'         => $this->faker->word . ' TechPro',
            'connection'    => $this->faker->randomElement(['Bluetooth', 'USB-C', 'Wireless']),
            'layout'        => $this->faker->randomElement(['Compact', 'Full-size', 'Tenkeyless']),
            'switch'        => $this->faker->randomElement(['Mechanical', 'Membrane']),
            'lighting'      => $this->faker->randomElement(['RGB', 'Single-color']),
            'compatibility' => $this->faker->randomElement(['Windows', 'MacOS', 'Linux']),
            'dimensions'    => $this->faker->randomElement(['45x15x5 cm', '40x13x4 cm']),
            'weight'        => $this->faker->randomElement(['1.2 kg', '1.5 kg']),
            'warranty'      => $this->faker->randomElement(['1 year', '2 years']),
        ];
    }
}
