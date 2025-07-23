<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function multiSearch(Request $request)
    {
       $query = $request->query('q');

        if (!$query) {
            return response()->json([], 200);
        }

        $results = $this->searchService->searchAll($query);
        return response()->json($results);
    }
    
}
