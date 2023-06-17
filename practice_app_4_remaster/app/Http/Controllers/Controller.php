<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    const DEFAULT_SEARCH_STRING = 'search?';
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
                dd($e);
                abort(500);
            }
        }
    }
}
