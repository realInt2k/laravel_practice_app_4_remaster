<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\PaginationDeleteRequest;
use App\Services\CategoryService;

class ProductController extends Controller
{
    const PER_PAGE = 15;

    public ProductService $productService;
    public CategoryService $categoryService;
    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $pathWithSearchParam = $this->getSearchString($request);
        if ($pathWithSearchParam == self::DEFAULT_SEARCH_STRING) {
            $pathWithSearchParam = 'products';
        }
        $categoryIds = [];
        if(isset($request->category))
        {
            $categoryIds = $this->categoryService->getAllRelevantIdsFromCategoryId($request->category);
        }
        $products = $this->productService->search($request, self::PER_PAGE, $pathWithSearchParam, $categoryIds);
        $categories = $this->categoryService->getAllCategories();
        $oldFilter = $request->all();
        return view(
            'products.index',
            compact('products', 'categories', 'oldFilter')
        );
    }

    public function show(Request $request, $id)
    {
        $product = $this->productService->getById($id);
        $redirectRequest = $request->all();
        return view('products.show', compact('product', 'redirectRequest'));
    }

    public function edit(Request $request, $id)
    {
        $product = $this->productService->edit($id);
        $categories = $this->categoryService->getAllCategories();
        $redirectRequest = $request->all();
        return view('products.edit', compact('product', 'categories', 'redirectRequest'));
    }

    public function create(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();
        return view('products.create', compact('categories'));
    }

    public function storeAjaxValidation(StoreProductRequest $request)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        return response(Response::HTTP_OK);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->store($request);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->route('products.show', $product->id);
    }

    public function updateAjaxValidation(UpdateProductRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $user = $this->productService->updateAjaxValidation($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_OK);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }

    public function updateAjax(UpdateProductRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $product = $this->productService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response()->json([
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function destroyAjax(Request $request, $id)
    {
        try {
            $this->productService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_NO_CONTENT);
    }

    public function destroy(PaginationDeleteRequest $request, $id)
    {
        try {
            $this->productService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }
}
