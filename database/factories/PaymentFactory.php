<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'method' => $this->faker->randomElement(['COD', 'VNPay', 'Momo', 'PayPal']),
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'payment_date' => now(),
            'created_at' => now(),
        ];
    }
}
