<?php
namespace App\Repositories;

use App\Models\ProductSpecification;

class ProductSpecificationRepository
{
    public function getByProductId($productId)
    {
        return ProductSpecification::where('product_id', $productId)->first();
    }
}
