<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function findById($id)
    {
        return Category::findOrFail($id);
    }
    public function getCategoriesByID()
    {
        return Category::whereHas('blogs')->get();
    }
}
