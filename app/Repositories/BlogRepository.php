<?php
namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{

    protected $blogModel;

    public function __construct(Blog $blogModel)
    {
        $this->blogModel = $blogModel;
    }

    public function getAllBlogs()
    {
        return Blog::with('author')->get();
    }

    public function getBlogStatus($limit = 3)
    {
        return $this->blogModel
        ->with('author') 
            ->where("status", "Lastest New")
            ->orderBy("publish_date", "desc")
            ->take($limit)
            ->get();
    }
     public function findById($id)
    {
        return Blog::find($id);
    }

    public function getAll()
    {
        return Blog::orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        return Blog::create($data);
    }

    public function update($id, array $data)
    {
        $blog = Blog::find($id);
        if (!$blog) return null;
        $blog->update($data);
        return $blog;
    }

    public function delete($id)
    {
        $blog = Blog::find($id);
        if (!$blog) return false;
        return $blog->delete();
    }
}