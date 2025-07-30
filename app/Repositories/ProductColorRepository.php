<?php
namespace App\Repositories;

use App\Models\ProductColor;

class ProductColorRepository
{
    public function getColorsByProduct($productId)
    {
        return ProductColor::where('product_id', $productId)->pluck('color');
    }
}