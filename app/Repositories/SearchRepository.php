<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SearchRepository
{
    public function searchProducts(string $query)
    {
        return Product::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->select('id', 'name', 'price', DB::raw('"product" as type'))
            ->get();
    }

    public function searchBlogs(string $query)
    {
        return Blog::where('title', 'like', "%$query%")
            ->orWhere('content', 'like', "%$query%")
            ->select('id', 'title as name', DB::raw('null as price'), DB::raw('"blog" as type'))
            ->get();
    }

    public function searchCategories(string $query)
    {
        return Category::where('name', 'like', "%$query%")
            ->select('id', 'name', DB::raw('null as price'), DB::raw('"category" as type'))
            ->get();
    }
}
