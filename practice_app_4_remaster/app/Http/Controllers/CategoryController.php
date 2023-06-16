<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public $categoryService;

    const PER_PAGE = 15;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->store($request);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->route('categories.show', $category->id);
    }

    public function storeAjaxValidation(StoreCategoryRequest $request)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        return response(Response::HTTP_OK);
    }

    public function index(Request $request)
    {
        $searchParams = $this->getSearchString($request);
        if ($searchParams == self::DEFAULT_SEARCH_STRING) {
            $searchParams = 'categories';
        }
        $oldFilter = $request->all();
        $categories = $this->categoryService->search($request, self::PER_PAGE, $searchParams);
        return view('categories.index', compact('categories', 'oldFilter'));
    }

    public function show(Request $request, $id)
    {
        try {
            $category = $this->categoryService->getById($id);
            $redirectRequest = $request->all();
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return view('categories.show', compact('category', 'redirectRequest'));
    }

    public function edit(Request $request, $id)
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            $category = $this->categoryService->getById($id);
            $redirectRequest = $request->all();
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return view('categories.edit', compact('redirectRequest', 'category', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();
        $redirectRequest = $request->all();
        return view('categories.create', compact('categories', 'redirectRequest'));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $category = $this->categoryService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }

    public function destroyAjax(Request $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $category = $this->categoryService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_OK);
    }

    public function updateAjaxValidation(UpdateCategoryRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $category = $this->categoryService->updateAjaxValidation($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_OK);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }

    public function updateAjax(UpdateCategoryRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $category = $this->categoryService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response()->json([
            'data' => $category
        ], Response::HTTP_OK);
    }
}
