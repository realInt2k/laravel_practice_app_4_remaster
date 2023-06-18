<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class CategoryRepository extends BaseRepository
{

    const CATEGORIES_PER_PAGE = 10;
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Category::class;
    }

    public function getAll()
    {
        return $this->all();
    }

    public function getAllRelevantIdsFromCategoryId($id): array
    {
        $category = $this->findOrFail($id);
        $childIds = $category->getAllChildIds();
        return array_merge([$id], $childIds);
    }

    public function saveNewCategory($request)
    {
        $category = $this->create($request->all());
        return $category;
    }

    public function destroyCategory($id)
    {
        $category = $this->findOrFail($id);
        foreach ($category->children as $cat) {
            $cat->update(['parent_id' => null]);
        }
        $category->delete();
        return $category;
    }

    public function updateCategory($input, $id)
    {
        $category = $this->findOrFail($id);
        $category->update($input);
        return $category;
    }

    public function search($searchData)
    {
        $categories = $this->model->query()->whereName($searchData['name']);
        $categories = $categories->paginate($searchData['perPage']);
        return $categories;
    }
}
