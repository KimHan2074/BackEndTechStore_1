<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogContronller extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService){
       $this ->blogService = $blogService;
    }

    public function index (){
        $blogs = $this ->blogService ->getAllBlogs();
        return response()->json($blogs);
    }

    public function getStatusBlog(){
        $blogs = $this ->blogService ->getBlogOFStatus();
        return response()->json($blogs);
    }

     public function show($id)
    {
        $blog = $this->blogService->getBlogById($id);

        if (!$blog) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($blog);
    }
    
}
