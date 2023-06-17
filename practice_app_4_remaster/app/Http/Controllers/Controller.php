<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    const DEFAULT_SEARCH_STRING = 'search?';
    const PER_PAGE = 5;

    public function getSearchString($request)
    {
        $pathWithSearchParam = self::DEFAULT_SEARCH_STRING;
        foreach ($request->all() as $searchParam => $searchValue) {
            if ($searchParam == 'page') {
                continue;
            }
            $pathWithSearchParam .= '&' . $searchParam . $searchValue;
        }
        return $pathWithSearchParam;
    }

    public function responseWithHtml($html, $status = Response::HTTP_OK, $message = null)
    {
        return response()->json([
            'html' => $html,
            'message' => $message,
        ], $status);
    }

    public function responseWithData($data, $status = Response::HTTP_OK, $message = "OK")
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function responseWhenException(Request $request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 404);
            } else {
                abort(404);
            }
        } else {
            if ($request->ajax()) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            } else {
                report($e);
                abort(500);
            }
        }
    }
}
