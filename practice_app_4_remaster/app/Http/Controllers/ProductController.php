<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function index()
    {
        return view('pages.products.index');
    }

    public function search(Request $request)
    {
        $categoryIds = [];
        if (isset($request->category)) {
            $categoryIds = $this->categoryService->getAllRelevantIdsFromCategoryId($request->category);
        }
        $products = $this->productService->search($request, self::PER_PAGE, $categoryIds);
        $categories = $this->categoryService->getAllCategories();
        $oldFilter = $request->all();
        $viewHtml = view(
            'pages.products.pagination',
            compact('products', 'categories', 'oldFilter')
        )->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function show(Request $request, $id)
    {
        $product = $this->productService->getById($id);
        $viewHtml = view('pages.products.show', compact('product'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function edit(Request $request, $id)
    {
        $product = $this->productService->edit($id);
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('pages.products.edit', compact('product', 'categories'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function create(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();
        $viewHtml = view('pages.products.create', compact('categories'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request);
        $viewHtml = view('pages.products.show', compact('product'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($product);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->productService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithHtml('', Response::HTTP_NO_CONTENT);
    }
}
