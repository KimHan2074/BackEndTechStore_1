<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'order_date' => now(),
            'status' => $this->faker->randomElement(['processing', 'completed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),

            // Thêm các trường mới
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'province' => 'Đà Nẵng',
            'district' => 'Hải Châu',
            'ward' => 'Thạch Thang',

            'created_at' => now(),
        ];
    }
}
