<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    const PER_PAGE = 5;

    public function responseWithData($data, $status = Response::HTTP_OK, $message = "OK")
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function responseWhenException(Request $request, Exception $e)
    {
        report($e);
        throw ($e);
    }
}
