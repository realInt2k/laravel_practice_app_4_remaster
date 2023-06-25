<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ProcessImageTrait;
use App\Repositories\ProductRepository;
use App\Http\Requests\StoreProductRequest;

class ProductService extends BaseService
{
    use ProcessImageTrait;

    protected $productRepo;

    public function __construct(
        ProductRepository $productRepo,
    ) {
        $this->productRepo = $productRepo;
    }

    public function getById($id)
    {
        $product = $this->productRepo->findOrFail($id);
        return $product;
    }

    public function store(StoreProductRequest $request)
    {
        $storeData = $request->all();
        $storeData['user_id'] = auth()->user()->id;
        $storeData['category_ids'] = $this->extractCategoryIdsFromInput($storeData);
        $storeData['image'] = $this->saveFile($request);
        $product = $this->productRepo->saveNewProduct($storeData);
        $product->syncCategories($storeData['category_ids']);
        return $product;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $updateData = $request->all();
            $product = $this->productRepo->findOrFail($id);
            $updateData['category_ids'] = $this->extractCategoryIdsFromInput($updateData);
            $updateData['image'] = $this->updateFile($request, $product->image);
            $product = $this->productRepo->updateProduct($updateData, $id);
            $product->syncCategories($updateData['category_ids']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update product', $e);
        }
        DB::commit();
        return $product;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepo->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot destroy product', $e);
        }
        DB::commit();

        $this->deleteFile($product->image);
        return $product;
    }

    public function unAttachUser(int $userId): void {
        $this->productRepo->unAttachUser($userId);
    }

    public function search($request, $perPage, $categoryIds)
    {
        $searchData = [];
        $searchData['category_ids'] = $categoryIds;
        $searchData['id'] = $request->id;
        $searchData['user_id'] = $request->user_id;
        $searchData['name'] = $request->name;
        $searchData['description'] = $request->description;
        $searchData['perPage'] = $perPage;
        $products = $this->productRepo->search($searchData);
        return $products;
    }

    private function extractCategoryIdsFromInput($input)
    {
        return isset($input['category_ids']) ? $input['category_ids'] : [];
    }
}
