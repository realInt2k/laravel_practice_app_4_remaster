<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    const PER_PAGE = 5;

    public function responseJSON(
        string|object $data,
        int $status = Response::HTTP_OK,
        string $message = "OK"
    ): JsonResponse {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function responseWhenException(Request $request, Exception $e)
    {
        throw ($e);
    }

    /**
     * @throws Exception
     */
    public function throwException(string $message, Exception $e): void
    {
        throw new Exception($message . ": " . $e->getMessage());
    }
}
