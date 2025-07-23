<?php

// app/Http/Controllers/Admin/AdminReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getUserReviews()
    {
        $data = $this->reviewService->getUserReviews();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    public function deleteReview($id)
{
    $result = $this->reviewService->deleteReview($id);
    return response()->json([
        'status' => $result
    ]);
}

}
