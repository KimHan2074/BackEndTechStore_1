<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::factory(1)->create();
        \App\Models\Category::factory(10)->create();
        \App\Models\Product::factory(20)->create();
        \App\Models\Order::factory(10)->create();
        \App\Models\OrderDetail::factory(30)->create();
        \App\Models\Review::factory(15)->create();
        \App\Models\Blog::factory(10)->create();
        \App\Models\Payment::factory(10)->create();
        \App\Models\ProductDescription::factory(10)->create();
        \App\Models\ProductSpecification::factory(10)->create();
        \App\Models\ProductFavorite::factory(10)->create();
        \App\Models\ProductImage::factory(20)->create();
        \App\Models\ProductColor::factory(10)->create();
        \App\Models\ProductCart::factory(10)->create();
        \App\Models\Author::factory(10)->create();
        \App\Models\Coupon::factory(10)->create();
    }
}
