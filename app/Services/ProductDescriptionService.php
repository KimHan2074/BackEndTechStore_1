<?php

namespace App\Services;

use App\Repositories\ProductDescriptionRepository;

class ProductDescriptionService
{
    protected $descriptionRepo;

    public function __construct(ProductDescriptionRepository $descriptionRepo)
    {
        $this->descriptionRepo = $descriptionRepo;
    }

    public function getByProductId($productId)
    {
        return $this->descriptionRepo->getByProductId($productId);
    }
}
