<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getReviewsByProduct($productId): JsonResponse
    {
        $reviews = $this->reviewService->getReviewsByProductId($productId);
        return response()->json([
            'product_id' => (int) $productId,
            'total_reviews' => $reviews->count(),
            'data' => $reviews
        ]);
    }
    
    public function storeReview(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'file' => 'nullable|file|image|max:5120',
        ]);

        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Upload ảnh nếu có
        $imageUrl = null;
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                $uploadResult = Cloudinary::uploadApi()->upload($request->file('file')->getRealPath(), [
                    'folder' => 'Reviews',
                ]);
                $imageUrl = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                    'exception' => $e->getTraceAsString(),
                    'config' => config('cloudinary'),
                    'env_values' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ]
                ]);
                return response()->json(['error' => 'Unable to upload image to Cloudinary'], 500);
            }
        }

        // Gọi service xử lý lưu review
        $this->reviewService->createReview($user->id, $productId, [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
            'image_url' => $imageUrl,
        ]);

        return response()->json(['message' => 'Review submitted successfully.'], 201);
    } 
}
