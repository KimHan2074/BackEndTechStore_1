<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductColorService;

class ProductColorController extends Controller
{
    protected $productColorService;

    public function __construct(ProductColorService $productColorService)
    {
        $this->productColorService = $productColorService;
    }

    public function getColorsByProduct($productId)
    {
        $colors = $this->productColorService->getColorsByProduct($productId);

        return response()->json([
            'success' => true,
            'colors' => $colors
        ]);
    }
}