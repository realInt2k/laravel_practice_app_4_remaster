<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ImageProcessing;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;

class ProductService extends BaseService
{
    use ImageProcessing;

    protected $productRepo;

    public function __construct(
        ProductRepository $productRepo,
    ) {
        $this->productRepo = $productRepo;
    }

    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['user_id'] = auth()->user()->id;
            $input['category_ids'] = $this->extractCategoryIdsFromInput($input);
            $this->processRequestImageToInput($request, $input, null);
            $product = $this->productRepo->saveNewProduct($input);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update product data");
        }
        DB::commit();
        return $product;
    }

    public function getById($id)
    {
        $product = $this->productRepo->findOrFail($id);
        return $product;
    }

    public function edit($id)
    {
        $product = $this->productRepo->findOrFail($id);
        return $product;
    }

    public function updateAjaxValidation($id)
    {
        $user = $this->productRepo->findOrFail($id);
        return $user;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['category_ids'] = $this->extractCategoryIdsFromInput($input);
            $product = $this->productRepo->findOrFail($id);
            $this->processRequestImageToInput($request, $input, $product);
            $product = $this->productRepo->updateProduct($input, $id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update product data");
        }
        DB::commit();
        return $product;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->productRepo->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot destroy product data");
        }
        DB::commit();
    }

    public function search($request, $perPage, $path, $categoryIds)
    {
        $searchData = [];
        $searchData['category_ids'] = $categoryIds;
        $searchData['id'] = $request->id;
        $searchData['user_id'] = $request->user_id;
        $searchData['name'] = $request->name;
        $searchData['description'] = $request->description;
        $searchData['perPage'] = $perPage;
        $searchData['path'] = $path;
        $products = $this->productRepo->search($searchData);
        return $products;
    }

    private function removeProductImage($product)
    {
        if ($product) {
            $this->removeFileFromPublicStorage('images/' . $product->image);
        }
    }

    private function processRequestImageToInput($request, &$input, $product)
    {
        $this->removeProductImage($product);
        if (isset($request->remove_image_request) && $request->remove_image_request == 'true') {
            $input['image'] = null;
        } else {
            if ($request->image !== null) {
                $path = $this->createPublicDirIfNotExist('storage/images/');
                $name = $this->nameTheImage($request);
                $image = $this->makeImage($request->file('image'));
                $this->resizeImage($image, 300, null, true);
                if (!file_exists($path . $name)) {
                    $this->saveImage($image, $path . $name);
                }
                $input['image'] = $name;
            } else {
                $input['image'] = null;
            }
        }
    }

    private function extractCategoryIdsFromInput($input)
    {
        if (!isset($input['category_ids'])) {
            return [];
        }
        $category_ids = $input['category_ids'];
        if (is_string($category_ids)) {
            return explode(',', $category_ids);
        } elseif ($category_ids == null || $category_ids == '') {
            return [];
        } else {
            return $category_ids;
        }
    }
}
