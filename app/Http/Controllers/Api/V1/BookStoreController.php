<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserBookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\ElasticsearchService;

class BookStoreController extends Controller
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * Using ORM : Book listing with pagination and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $books = Book::query();

            // Dynamic filters
            $filters = $request->input('filters', []);
            foreach ($filters as $field => $value) {
                if ($value) {
                    $books->where($field, 'like', '%' . $value . '%');
                }
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $books = $books->paginate($perPage);

            $this->paging = true;
            return $this->sendResponse(
                UserBookResource::collection($books),
                true
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }


    /**
     * Using Elasticsearch : Book listing with pagination and search 
     */
    public function search(Request $request)
    {
        $index = 'books'; // Specify your Elasticsearch index name

        // Pagination parameters
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $perPage = $request->input('perPage', 9); // Default to 10 items per page

        // Calculate the offset
        $from = ($page - 1) * $perPage;

        // Get dynamic filters
        $filters = $request->input('filters', []);

        // Define allowed fields
        $allowedFields = ['title', 'published', 'author', 'genre', 'isbn', 'publisher'];

        // Filter out any fields not in the allowed list
        $validFilters = collect($filters)->filter(function ($value, $field) use ($allowedFields) {
            return in_array($field, $allowedFields) && $value;
        })->all();


        // Build the Elasticsearch query dynamically based on valid filters
        $query = [
            'query' => [
                'bool' => [
                    'must' => collect($validFilters)->map(function ($value, $field) {
                        return [
                            'match' => [
                                $field => $value,
                            ],
                        ];
                    })->values()->all(), // Convert the collection to array and re-index
                ],
            ],
            'from' => $from,
            'size' => $perPage,
        ];


        // return $this->sendResponse($query);
        $resultSet = $this->elasticsearchService->search($index, $query);

        $data = [];
        foreach ($resultSet->getResults() as $result) {
            // Extract the source data from each Elasticsearch document
            $data[] = $result->getSource();
        }

        // Get the total number of hits for pagination information
        $totalHits = $resultSet->getTotalHits();

        // Calculate the total number of pages
        $totalPages = ceil($totalHits / $perPage);

        // Return the results and pagination information as JSON
        return $this->sendResponse(
            [
                'items' => $data,
                'pagination' => [
                    'total' => $totalHits,
                    'count' => count($data),
                    'per_page' => $perPage,
                    'current_page' => (int) $page,
                    'total_pages' => $totalPages,
                ]
            ]
        );
    }


    /**
     * Get book details by id
     *
     */
    public function bookDetail($id): JsonResponse
    {
        try {
            //get index by id
            $book = $this->elasticsearchService->getDocument('books', $id);
            return $this->sendResponse(
                new UserBookResource($book),
                true
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }
}
