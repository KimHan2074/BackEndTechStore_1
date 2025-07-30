<?php
namespace App\Services;

use App\Repositories\ProductColorRepository;

class ProductColorService
{
    protected $productColorRepo;

    public function __construct(ProductColorRepository $productColorRepo)
    {
        $this->productColorRepo = $productColorRepo;
    }

    public function getColorsByProduct($productId)
    {
        return $this->productColorRepo->getColorsByProduct($productId);
    }
}