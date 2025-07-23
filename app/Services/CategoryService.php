<?php
namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getCategoriesByID()
    {
        return $this->categoryRepo->getCategoriesByID();
    }
    

    public function getAll()
    {
        return $this->categoryRepo->getAllCategories();
    }

    public function getById($id)
    {
        return $this->categoryRepo->findById($id);
    }
}
