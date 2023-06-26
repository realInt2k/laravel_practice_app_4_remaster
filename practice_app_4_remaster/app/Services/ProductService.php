<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ProcessImageTrait;
use App\Repositories\ProductRepository;
use App\Http\Requests\StoreProductRequest;

class ProductService extends BaseService
{
    use ProcessImageTrait;

    protected ProductRepository $productRepo;

    public function __construct(
        ProductRepository $productRepo,
    ) {
        $this->productRepo = $productRepo;
    }

    public function getById(int $id): Product
    {
        return $this->productRepo->findOrFail($id);
    }

    public function store(StoreProductRequest $request): Product
    {
        $storeData = $request->all();
        $storeData['user_id'] = auth()->user()->id;
        $storeData['category_ids'] = $this->extractCategoryIdsFromInput($storeData);
        $storeData['image'] = $this->saveFile($request);
        $product = $this->productRepo->saveNewProduct($storeData);
        $product->syncCategories($storeData['category_ids']);
        return $product;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request, int $id): Product
    {
        $oldImage = null;
        try {
            $updateData = $request->all();
            $product = $this->productRepo->findOrFail($id);
            $updateData['category_ids'] = $this->extractCategoryIdsFromInput($updateData);
            $oldImage = $product->image;
            $updateData['image'] = $this->updateFile($request, $oldImage, true);
            $product = $this->productRepo->updateProduct($updateData, $id);
            $product->syncCategories($updateData['category_ids']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update product', $e);
        }
        DB::commit();
        $this->updateFile($request, $oldImage);
        return $product;
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Product
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

    public function search(Request $request, int $perPage, array $categoryIds): LengthAwarePaginator
    {
        $searchData = [];
        $searchData['category_ids'] = $categoryIds;
        $searchData['id'] = $request->id;
        $searchData['user_id'] = $request->user_id;
        $searchData['name'] = $request->name;
        $searchData['description'] = $request->description;
        $searchData['perPage'] = $perPage;
        return $this->productRepo->search($searchData);
    }

    private function extractCategoryIdsFromInput(array $input): array
    {
        return $input['category_ids'] ?? [];
    }
}
