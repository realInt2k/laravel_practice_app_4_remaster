<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
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
            if($searchParam == 'page') {
                continue;
            }
            $pathWithSearchParam .= '&' . $searchParam . $searchValue;
        }
        return $pathWithSearchParam;
    }
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function deleteGetRedirectPage($curPage, $lastPage, $perPage, $href, $count)
    {
        $redirectPage = $curPage;
        if ($count == 1 && $curPage == $lastPage && $curPage != 1) {
            $redirectPage = $curPage - 1;
        }
        return $redirectPage;
    }

    /**
     * @paramExplain $count means how many elements are presented in that page
     * @return return url for redirect based on pagination information
     */
    public function deleteGetRedirectUrl(int $curPage, int $lastPage, int $perPage, string $href, int $count): string
    {
        $redirectPage = $this->deleteGetRedirectPage($curPage, $lastPage, $perPage, $href, $count);
        if ($href === route('users.index') || $href === route('products.index') || $href === route('roles.index')) {
            $url = $href . "?&page=" . $redirectPage;
        } else {
            $url = $href . "&page=" . $redirectPage;
        }
        return $url;
    }

    /**
     * @paramExplain $count means how many elements are presented in that page
     * @return return url for redirect based on pagination information
     */
    public function getRedirectUrl($curPage, $href): string
    {
        $redirectPage = $curPage;
        if ($href === route('users.index') || $href === route('products.index') || $href === route('roles.index')) {
            $url = $href . "?&page=" . $redirectPage;
        } else {
            $url = $href . "&page=" . $redirectPage;
        }
        return $url;
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
