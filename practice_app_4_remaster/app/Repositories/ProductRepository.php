<?php

namespace App\Repositories;

use App\Models\Product;

//use Your Model

/**
 * Class ProductRepository.
 */
class ProductRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Product::class;
    }

    public function unAttachUser(int $userId): void
    {
        $this->model->where('user_id', $userId)->update([
            'user_id' => null
        ]);
    }

    public function saveNewProduct($storeData)
    {
        return $this->create($storeData);
    }


    public function updateProduct($updateData, $id)
    {
        $product = $this->findOrFail($id);
        $product->update($updateData);
        return $product;
    }

    public function search($searchData)
    {
        return $this->model->withCategories()
            ->whereCategoryIds($searchData['category_ids'])
            ->whereId($searchData['id'])
            ->whereUserId($searchData['user_id'])
            ->whereName($searchData['name'])
            ->whereDescription($searchData['description'])
            ->paginate($searchData['perPage']);
    }

    public function destroy($id)
    {
        $product = $this->findOrFail($id);
        $product->delete();
        return $product;
    }
}
