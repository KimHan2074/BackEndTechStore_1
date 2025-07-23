<?php

namespace App\Services;

use App\Repositories\ProductFavoriteRepository;

class ProductFavoriteService
{
    protected $favoriteRepo;

    public function __construct(ProductFavoriteRepository $favoriteRepo)
    {
        $this->favoriteRepo = $favoriteRepo;
    }

    public function getUserWishlist($userId)
    {
        return $this->favoriteRepo->getWishlistByUserId($userId);
    }
}
