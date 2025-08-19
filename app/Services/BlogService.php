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
     public function getAll()
    {
        return $this->blogRepository->getAll();
    }

    public function getById($id)
    {
        return $this->blogRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->blogRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->blogRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->blogRepository->delete($id);
    }
}