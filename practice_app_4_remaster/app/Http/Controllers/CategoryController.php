<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application as FoundationApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): View|FoundationApplication|Factory|Application
    {
        return view('pages.categories.index');
    }

    public function search(Request $request): JsonResponse
    {
        $categories = $this->categoryService->search($request, self::PER_PAGE);
        $viewHtml = view('pages.categories.pagination', compact('categories'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);
        $viewHtml = view('pages.categories.show', compact('category'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function edit(int $id): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        $category = $this->categoryService->getById($id);
        $viewHtml = view('pages.categories.edit', compact('category', 'categories'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function create(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('pages.categories.create', compact('categories'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store($request);
        return $this->responseJSON($category);
    }

    /**
     * @throws Exception
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryService->update($request, $id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update category', $e);
        }
        DB::commit();
        return $this->responseJSON($category);
    }

    /**
     * @throws Exception
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryService->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot destroy category', $e);
        }
        DB::commit();
        return $this->responseJSON($category);
    }
}
