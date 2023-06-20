<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;

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

    public function saveNewCategory($storeData)
    {
        $category = $this->create($storeData);
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

    public function updateCategory($updateData, $id)
    {
        $category = $this->findOrFail($id);
        $category->update($updateData);
        return $category;
    }

    public function search($searchData)
    {
        $categories = $this->model->query()->whereName($searchData['name']);
        $categories = $categories->paginate($searchData['perPage']);
        return $categories;
    }
}
