<?php
namespace App\Repositories;

use App\Models\ProductDescription;

class ProductDescriptionRepository
{
    public function getByProductId($productId)
    {
        return ProductDescription::where('product_id', $productId)->first();
    }
}
