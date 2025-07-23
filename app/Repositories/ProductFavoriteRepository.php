<?php

namespace App\Repositories;

use App\Models\ProductFavorite;

class ProductFavoriteRepository
{
    public function getWishlistByUserId($userId)
    {
        return ProductFavorite::with([
            'product' => function ($query) {
                $query->select('id', 'name', 'description', 'price')
                      ->with(['images' => function ($q) {
                          $q->select('id', 'product_id', 'image_url')->take(1); // Lấy 1 ảnh đại diện
                      }]);
            }
        ])
        ->where('user_id', $userId)
        ->get();
    }
}
