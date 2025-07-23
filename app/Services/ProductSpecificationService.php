<?php
namespace App\Services;

use App\Repositories\ProductSpecificationRepository;

class ProductSpecificationService
{
    protected $specRepo;

    public function __construct(ProductSpecificationRepository $specRepo)
    {
        $this->specRepo = $specRepo;
    }

    public function getByProductId($productId)
    {
        return $this->specRepo->getByProductId($productId);
    }
}
