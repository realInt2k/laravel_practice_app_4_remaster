<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\CategoryRepository;

class CategoryService extends BaseService
{
    protected $categoryRepo, $userRepo;

    public function __construct(
        CategoryRepository $categoryRepo
    ) {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories()
    {
        return $this->categoryRepo->all();
    }

    public function getById($id)
    {
        $category = $this->categoryRepo->findOrFail($id);
        return $category;
    }

    public function getAllRelevantIdsFromCategoryId($id): array
    {
        $category = $this->categoryRepo->findOrFail($id);
        $childIds = $category->getAllChildIds();
        return $childIds;
    }

    public function store($request)
    {
        $storeData = $request->all();
        $category = $this->categoryRepo->saveNewCategory($storeData);
        return $category;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepo->destroyCategory($id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot delete category data");
        }
        DB::commit();
        return $category;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $updateData = $request->all();
            $category = $this->categoryRepo->updateCategory($updateData, $id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update category data");
        }
        DB::commit();
        return $category;
    }

    public function search($request, $perPage)
    {
        $searchData = [];
        $searchData['name'] = $request->name;
        $searchData['perPage'] = $perPage;
        return $this->categoryRepo->search($searchData);
    }
}
