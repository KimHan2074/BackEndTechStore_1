<?php
namespace App\Repositories;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewRepository
{
    public function getByProductId($productId)
    {
        return Review::with('user')
                     ->where('product_id', $productId)
                     ->orderByDesc('review_date')
                     ->get(); 
    }

    public function create(array $data)
    {
        \Log::info('Review Data:', $data);
        return Review::create($data);
    }
    public function getUserReviewsWithInfo()
    {
        return DB::table('reviews')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->select(
                'reviews.id',
                'users.name',
                'users.email',
                'reviews.rating',
                'reviews.comment',
                'reviews.review_date'
            )
            ->orderBy('reviews.review_date', 'desc')
            ->get();
    }
    public function deleteReviewById($id)
    {
        return DB::table('reviews')->where('id', $id)->delete();
    }
}
