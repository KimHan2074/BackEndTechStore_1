<?php 
namespace App\Services;

use App\Repositories\BlogRepository;
class BlogService {

    protected $blogRepository;

    public function __construct(BlogRepository $blogRepository){
        $this ->blogRepository = $blogRepository;
    }

    public function getAllBlogs(){
        return $this->blogRepository ->getAllBlogs();
    }

    public function getBlogOFStatus(){
        return $this ->blogRepository ->getBlogStatus();
    }
    public function getBlogById($id)
    {
        return $this->blogRepository->findById($id);
    }
}