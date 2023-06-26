<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application as FoundationApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\CategoryService;

class ProductController extends Controller
{
    public ProductService $productService;
    public CategoryService $categoryService;
    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(): View|FoundationApplication|Factory|Application
    {
        $categories = $this->categoryService->getAllCategories();
        return view('pages.products.index', compact('categories'));
    }

    public function search(Request $request): JsonResponse
    {
        $categoryIds = [];
        if (isset($request->category)) {
            $categoryIds = $this->categoryService->getAllRelevantIdsFromCategoryId($request->category);
        }
        $products = $this->productService->search($request, self::PER_PAGE, $categoryIds);
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view(
            'pages.products.pagination',
            compact('products', 'categories')
        )->render();
        return $this->responseJSON($viewHtml);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);
        $viewHtml = view('pages.products.show', compact('product'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function edit(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('pages.products.edit', compact('product', 'categories'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function create(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('pages.products.create', compact('categories'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->store($request);
        return $this->responseJSON($product);
    }

    /**
     * @throws Exception
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseJSON($product);
    }

    /**
     * @throws Exception
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseJSON($product);
    }
}
