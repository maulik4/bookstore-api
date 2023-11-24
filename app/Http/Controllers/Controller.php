<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected $paging = false;

    protected function withPagination($data)
    {
        return [
            'items' => $data->collection,
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage()
            ],
        ];
    }

    /**
     * success response method.
     */
    public function sendResponse($result, $message = '', $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $this->paging ? $this->withPagination($result) : $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     */
    public function sendError($error, $code = 404, $errorMessages = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
