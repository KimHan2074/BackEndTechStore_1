<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['fixed', 'percent']);
        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $type === 'percent'
                ? $this->faker->numberBetween(5, 30) // 5% - 30%
                : $this->faker->numberBetween(10000, 100000), // 10k - 100k

            'start_date' => Carbon::now()->subDays(rand(1, 5)),
            'end_date' => Carbon::now()->addDays(rand(5, 10)),
        ];
    }
}
