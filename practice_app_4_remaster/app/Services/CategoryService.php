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

    public function store($request)
    {
        $dataStore = $request->all();
        $category = $this->categoryRepo->saveNewCategory($dataStore);
        return $category;
    }

    public function getById($id)
    {
        $category = $this->categoryRepo->findOrFail($id);
        return $category;
    }

    public function getAllRelevantIdsFromCategoryId($id): array
    {
        $ids = $this->categoryRepo->getAllRelevantIdsFromCategoryId($id);
        return $ids;
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

    public function updateAjaxValidation($request, $id)
    {
        $category = $this->categoryRepo->findOrFail($id);
        return $category;
    }

    public function update($request, $id)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $category = $this->categoryRepo->updateCategory($input, $id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update category data");
        }
        DB::commit();
        return $category;
    }

    public function search($request, $perPage, $path)
    {
        $searchData = [];
        $searchData['name'] = $request->name;
        $searchData['perPage'] = $perPage;
        $searchData['path'] = $path;
        return $this->categoryRepo->search($searchData);
    }
}
