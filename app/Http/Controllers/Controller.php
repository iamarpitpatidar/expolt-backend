<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * send success response.
     *
     * @param mixed $result
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse(mixed $result, int $code = 200): JsonResponse
    {
        $response = ['status' => 'success'];

        if (gettype($result) === 'array') {
            if (isset($result['message'])) {
                $response['message'] = $result['message'];
                array_splice($result, 0, 1);
            }
            $response['data'] = count($result) > 1 ? $result : $result[array_key_first($result)];
        } else {
            $response['message'] = $result;
        }

        return response()->json($response, $code);
    }

    /**
     * Send error response.
     *
     * @param  array<string>  $errorMessages
     */
    public function sendErrorResponse(string $message, int $errorCode = 500, array $errorMessages = []): JsonResponse
    {
        if ($errorCode === 200) {
            trigger_error('Are you reporting an error on a success request?', E_USER_WARNING);
        }

        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if (! empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $errorCode);
    }

    /**
     * Handle 403 Forbidden error.
     */
    public function forbiddenError(string $message = 'Forbidden'): JsonResponse
    {
        return $this->sendErrorResponse($message, 403);
    }

    /**
     * Handle 500 Internal Server Error.
     */
    public function internalError(string $message = 'Internal Error'): JsonResponse
    {
        return $this->sendErrorResponse($message, 500);
    }

    /**
     * Handle 404 Not Found error.
     */
    public function notFoundError(string $message = 'Not Found'): JsonResponse
    {
        return $this->sendErrorResponse($message, 404);
    }

    /**
     * Handle 401 Unauthorized error.
     */
    public function unauthorizedError(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->sendErrorResponse($message, 401);
    }

    /**
     * Handle 400 Bad Request error (Wrong Arguments).
     */
    public function badRequestError(string $message = 'Bad Request'): JsonResponse
    {
        return $this->sendErrorResponse($message, 400);
    }
}
