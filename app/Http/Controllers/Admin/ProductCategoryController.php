<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; 

class ProductCategoryController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    // ========== API GET theo category ==========

    // public function __construct(ProductService $productService)
    // {
    //     $this->productService = $productService;
    // }

    public function getSmartPhone()
    {
        return response()->json($this->productService->getProductsByCategoryId(1));
    }

    public function getLaptop()
    {
        return response()->json($this->productService->getProductsByCategoryId(2));
    }

    public function getHeadPhone()
    {
        return response()->json($this->productService->getProductsByCategoryId(3));
    }

    public function getKeyboard()
    {
        return response()->json($this->productService->getProductsByCategoryId(4));
    }

    public function getMouse()
    {
        return response()->json($this->productService->getProductsByCategoryId(5));
    }
    
    public function getCamera()
    {
        return response()->json($this->productService->getProductsByCategoryId(6));
    }

    public function getSmartWatch()
    {
        return response()->json($this->productService->getProductsByCategoryId(7));
    }

    public function getChargingAccessory()
    {
        return response()->json($this->productService->getProductsByCategoryId(8));
    }

    public function getTV()
    {
        return response()->json($this->productService->getProductsByCategoryId(9));
    }

    public function getAirConditioner()
    {
        return response()->json($this->productService->getProductsByCategoryId(10));
    }

    // ========== GET All categories ==========
    public function index()
    {
        try {
            $categories = $this->categoryService->getAll();
            return response()->json([
                'status' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch categories: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========== GET product by ID (dùng cho edit) ==========
    public function show($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            return response()->json([
                'status' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error("Product not found: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // ========== PUT: Update product ==========
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'old_price' => 'nullable|numeric',
                'promotion_type' => 'nullable|string',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|integer',
                'status' => 'required|in:In Stock,Low Stock,Out of Stock',
                'image_url' => 'nullable|url', // 👈 thêm dòng này
            ]);


            $product = $this->productService->updateProduct($id, $validated);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation failed:", $e->errors());

            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Update failed: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


   public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'old_price' => 'nullable|numeric',
            'promotion_type' => 'nullable|string',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'stock' => 'required|integer',
            'status' => 'required|string',
            'image_url' => 'nullable|string'
        ]);

        $product = Product::create($validated);

        return response()->json(['message' => 'Product created successfully', 'data' => $product]);
    } catch (\Exception $e) {
        Log::error($e);
        return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
    }
}

}
