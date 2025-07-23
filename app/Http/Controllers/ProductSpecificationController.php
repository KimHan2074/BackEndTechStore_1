<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ProductSpecificationService;

class ProductSpecificationController extends Controller
{
    protected $specService;

    public function __construct(ProductSpecificationService $specService)
    {
        $this->specService = $specService;
    }

    public function getByProductId($productId): JsonResponse
    {
        $spec = $this->specService->getByProductId($productId);

        if (!$spec) {
            return response()->json([
                'message' => 'Specification not found for product ID ' . $productId
            ], 404);
        }

        return response()->json($spec);
    }
}
