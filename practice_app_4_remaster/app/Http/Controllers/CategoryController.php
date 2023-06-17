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

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return view('pages.categories.index');
    }

    public function search(Request $request)
    {
        $searchParams = $this->getSearchString($request);
        if ($searchParams == self::DEFAULT_SEARCH_STRING) {
            $searchParams = 'categories';
        }
        $oldFilter = $request->all();
        $categories = $this->categoryService->search($request, self::PER_PAGE, $searchParams);
        $viewHtml = view('pages.categories.pagination', compact('categories', 'oldFilter'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function show(Request $request, $id)
    {
        $category = $this->categoryService->getById($id);
        $viewHtml = view('pages.categories.show', compact('category'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function edit(Request $request, $id)
    {
        $categories = $this->categoryService->getAllCategories();
        $category = $this->categoryService->getById($id);
        $viewHtml = view('categories.edit', compact('category', 'categories'));
        return $this->responseWithHtml($viewHtml);
    }

    public function create(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('categories.create', compact('categories'));
        return $this->responseWithHtml($viewHtml);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store($request);
        $viewHtml = view('pages.categories.show', compact('category'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        $viewHtml = view('pages.categories.show', compact('category'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $category = $this->categoryService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithHtml('', Response::HTTP_NO_CONTENT);
    }
}
