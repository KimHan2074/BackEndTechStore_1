<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ProductDescriptionService;

class ProductDescriptionController extends Controller
{
    protected $descriptionService;

    public function __construct(ProductDescriptionService $descriptionService)
    {
        $this->descriptionService = $descriptionService;
    }

    public function getByProductId($productId): JsonResponse
    {
        $desc = $this->descriptionService->getByProductId($productId);

        if (!$desc) {
            return response()->json([
                'message' => 'Description not found for product ID ' . $productId
            ], 404);
        }

        return response()->json($desc);
    }
}
