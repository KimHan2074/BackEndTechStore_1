<?php
namespace App\Services;

use App\Repositories\SearchRepository;

class SearchService
{
    protected $searchRepo;

    public function __construct(SearchRepository $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }

    public function searchAll(string $query)
    {
        $products = $this->searchRepo->searchProducts($query);
        $blogs = $this->searchRepo->searchBlogs($query);
        $categories = $this->searchRepo->searchCategories($query);

        return $products->concat($blogs)->concat($categories)->values();
    }
}
