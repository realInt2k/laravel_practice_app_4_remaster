<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository
{

    const CATEGORIES_PER_PAGE = 10;
    /**
     * @return string
     *  Return the model
     */
    public function model(): string
    {
        return Category::class;
    }

    public function saveNewCategory(array $storeData): Category
    {
        return $this->create($storeData);
    }

    public function detachFromParent(int $id): void
    {
        $this->model->where('parent_id', $id)->update(['parent_id'=>null]);
    }

    public function destroyCategory(int $id): Category
    {
        $category = $this->findOrFail($id);
        $this->detachFromParent($id);
        $category->delete();
        return $category;
    }

    public function updateCategory(array $updateData, int $id): Category
    {
        $category = $this->findOrFail($id);
        $category->update($updateData);
        return $category;
    }

    public function search(array $searchData): LengthAwarePaginator
    {

        return $this->model->whereName($searchData['name'])
            ->paginate($searchData['perPage']);
    }
}
