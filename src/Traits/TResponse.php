<?php

namespace Credpal\Expense\Traits;

use Credpal\Expense\Utilities\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Trait TResponder
 * @package Globals\Traits
 */

trait TResponse
{
    /**
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success(
        $data,
        string $message = 'Successful',
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(
        $data,
        $message = 'Unsuccessful',
        $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * @param Exception $e
     * @param int $statusCode
     * @return JsonResponse
     */
    public function fatalError(
        Exception $e,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        $line = $e->getTrace();

        $error = [
            'message' => $e->getMessage(),
            'trace' => $line[0],
            'mini_trace' => $line[1],
        ];

        if ('PRODUCTION' === strtoupper(config('APP_ENV'))) {
            $error = null;
        }

        $response = [
            'success' => false,
            'message' => 'Oops! Something went wrong on the server',
            'errors' => $error,
        ];

        return response()->json($response, $statusCode);
    }

    public function datatable(
        Builder $query,
        array $config = [],
        $resourceClass = null
    ): JsonResponse
    {
        $data = Datatable::make($query, $config, $resourceClass);

        if ($data instanceof BinaryFileResponse) {
            return $data;
        }

        $response = [
            'success' => true,
            'message' => 'Data Fetched Successfully',
            'datatable' => $data,
        ];

        return $this->success($response, Response::HTTP_OK);
    }
}
