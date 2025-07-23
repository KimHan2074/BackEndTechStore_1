<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Models\Category;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
     public function index()
    {
        $categories = Category::all(); 
        return response()->json($categories); 
    }

    public function getCategoriesByID()
    {
        $categories = $this->categoryService->getCategoriesByID();
        return response()->json($categories);
    }
    public function GetImage()
    {
        $categories = Category::all(['id','name','image_url']); 
        return response()->json($categories);
    }
}
