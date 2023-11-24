<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomizeValidationErrorResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->status() === 422) {
            $originalData = $response->getOriginalContent();
            if (isset($originalData['message']) && isset($originalData['errors'])) {
                $response->setData(
                    array_merge(
                        ['success' => false],
                        $originalData
                    )
                );
            }
        }

        if ($response instanceof JsonResponse && $response->status() === 404) {
            $originalData = $response->getOriginalContent();
            $modelName = $this->getModelClass($originalData['message']);
            return response()->json([
                'success' => false,
                'message' => $modelName . ' not found',
            ], 404);
        }

        return $response;
    }

    protected function getModelClass($message)
    {
        // Extracting the model class from the exception message
        preg_match('/No query results for model \[([\w\\\\]+)\]/', $message, $matches);

        return isset($matches[1]) ? class_basename($matches[1]) : null;
    }
}
