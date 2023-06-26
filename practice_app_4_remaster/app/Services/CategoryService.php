<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService extends BaseService
{
    protected CategoryRepository $categoryRepo;

    public function __construct(
        CategoryRepository $categoryRepo
    )
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories(): array|Collection
    {
        return $this->categoryRepo->all();
    }

    public function getById(int $id): Category
    {
        return $this->categoryRepo->findOrFail($id);
    }

    public function getAllRelevantIdsFromCategoryId(int $id): array
    {
        $category = $this->categoryRepo->findOrFail($id);
        return $category->getAllDescendantIds();
    }

    public function store(Request $request): Category
    {
        $storeData = $request->all();
        return $this->categoryRepo->saveNewCategory($storeData);
    }

    public function destroy(int $id): Category
    {
        return $this->categoryRepo->destroyCategory($id);
    }

    public function update(Request $request, int $id): Category
    {
        $updateData = $request->all();
        return $this->categoryRepo->updateCategory($updateData, $id);
    }

    public function search(Request $request, int $perPage): LengthAwarePaginator
    {
        $searchData = [];
        $searchData['name'] = $request->name;
        $searchData['perPage'] = $perPage;
        return $this->categoryRepo->search($searchData);
    }
}
