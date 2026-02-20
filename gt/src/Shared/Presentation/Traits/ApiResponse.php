<?php

namespace Shared\Presentation\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * core of response
     *
     * @param string $message
     * @param array|object|null $data
     * @param integer $statusCode
     * @param boolean $isSuccess
     * @return JsonResponse
     */
    public function coreResponse(string $message, $data = null, int $statusCode, bool $isSuccess = true): JsonResponse
    {
        // Check the params
        if (!$message)
            return response()->json(['message' => 'Message is required'], 500);

        // Send the response
        if ($isSuccess) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        }
    }

    /**
     * Send any success response
     *
     * @param string $message
     * @param array|object $data
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function success(string $message, $data, int $statusCode = 200): JsonResponse
    {
        return $this->coreResponse($message, $data, $statusCode);
    }

    /**
     * Send any error response
     *
     * @param string $message
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = 500, $data = null): JsonResponse
    {
        return $this->coreResponse($message, $data, $statusCode, false);
    }
}
