<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ImageProcessing;
use App\Repositories\ProductRepository;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Validation\UnauthorizedException;

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
            $this->extractCategoryIdsFromInput($input);
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
        /** @var User */
        $authUser = auth()->user();

        if (!$authUser->isAdmin() && !$authUser->isSuperAdmin() && !$authUser->hasProduct($id)) {
            throw new UnauthorizedException("not your product");
        }
        DB::beginTransaction();
        try {
            $input = $request->all();
            $this->extractCategoryIdsFromInput($input);
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

    private function removeProductImage($product)
    {
        if ($product) {
            $this->removeFileFromPublicStorage('images/' . $product->image);
        }
    }

    private function processRequestImageToInput($request, &$input, $product)
    {
        if (isset($request->remove_image_request) && $request->remove_image_request == 'true') {
            $this->removeProductImage($product);
            $input['image'] = null;
        } else {
            if ($request->image !== null) {
                $this->removeProductImage($product);
                $path = $this->createPublicDirIfNotExist('storage/images/');
                $name = $this->nameTheImage($request);
                $image = $this->makeImage($request->file('image'));
                $this->resizeImage($image, 300, null, true);
                if (!file_exists($path . $name)) {
                    $this->saveImage($image, $path . $name);
                }
                $input['image'] = $name;
            } else {
                // no remove image requested and request->image isn't null => no need to update.
            }
        }
    }

    private function extractCategoryIdsFromInput(&$input)
    {
        if (!isset($input['category_ids'])) {
            $input['category_ids'] = [];
        }
    }
}
