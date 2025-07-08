<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    /**
     * General method for API response
     * @param mixed $data The data you want to send in the response.
     * @param string|null $message A message to send with the response.
     * @param int $code The HTTP status code.
     * @param bool $status The status of the response.
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse($data = [], $message = null, $code = 200, $status = true)
    {
        return response()->json([
            'status' => $status,  // Indicates success or failure
            'message' => $message, // Optional message
            'data' => $data,       // The data to be sent in response
        ], $code);
    }
    /**
     * Return a success response with data.
     * @param mixed $data Data to return.
     * @param string|null $message Success message.
     * @param int $code HTTP status code (200 by default).
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = [], $message = 'Operation successful', $code = 200)
    {
        return $this->apiResponse($data, $message, $code, true);
    }

    /**
     * Return an error response with message.
     * @param string|null $message Error message.
     * @param int $code HTTP status code (default is 400 or 404, depending on the error).
     * @return \Illuminate\Http\JsonResponse
     */
    public function  errorResponse($message = 'An error occurred', $code = 400)
    {
        return $this->apiResponse([], $message, $code, false);
    }
}
