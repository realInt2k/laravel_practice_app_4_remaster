<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    const DEFAULT_SEARCH_STRING = 'search?';
    const PER_PAGE = 5;

    public function responseWithData($data, $status = Response::HTTP_OK, $message = "OK")
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }
    
    public function returnAjaxResponse($status, $e)
    {
        return response()->json([
            'error' => $e->getMessage()
        ], $status);
    }

    public function responseWhenException(Request $request, Exception $e)
    {
        report($e);
        if ($e instanceof ModelNotFoundException) {
            if ($request->ajax()) {
                $this->returnAjaxResponse(Response::HTTP_NOT_FOUND, $e);
            } else {
                abort(404);
            }
        } else if ($e instanceof UnauthorizedException) {
            if ($request->ajax()) {
                $this->returnAjaxResponse(Response::HTTP_UNAUTHORIZED, $e);
            } else {
                abort(Response::HTTP_UNAUTHORIZED);
            }
        } else {
            if ($request->ajax()) {
                $this->returnAjaxResponse(500, $e);
            } else {
                abort(500);
            }
        }
    }
}
