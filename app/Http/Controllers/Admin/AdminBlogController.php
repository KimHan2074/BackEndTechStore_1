<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use Illuminate\Http\Request;

class AdminBlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index()
    {
        $blogs = $this->blogService->getAll();
        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|string',
            'link_url' => 'nullable|string',
            'status' => 'required|in:active,lastest new',
            'author_id' => 'required|integer',
            'publish_date' => 'nullable|date',
        ]);

        $blog = $this->blogService->create($validated);

        return response()->json([
            'status' => true,
            'data' => $blog
        ]);
    }

    public function show($id)
    {
        $blog = $this->blogService->getById($id);
        if (!$blog) {
            return response()->json(['status' => false, 'message' => 'Blog not found'], 404);
        }
        return response()->json(['status' => true, 'data' => $blog]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|integer',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image_url' => 'nullable|string',
            'link_url' => 'nullable|string',
            'status' => 'sometimes|in:draft,published',
            'author_id' => 'sometimes|integer',
            'publish_date' => 'nullable|date',
        ]);

        $blog = $this->blogService->update($id, $validated);
        if (!$blog) {
            return response()->json(['status' => false, 'message' => 'Blog not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $blog]);
    }

    public function destroy($id)
    {
        $deleted = $this->blogService->delete($id);
        if (!$deleted) {
            return response()->json(['status' => false, 'message' => 'Blog not found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Blog deleted']);
    }
}
