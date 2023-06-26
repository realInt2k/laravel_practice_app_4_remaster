<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

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
    public function model(): string
    {
        return Product::class;
    }

    public function detachFromUser(int $userId): void
    {
        $this->model->where('user_id', $userId)->update([
            'user_id' => null
        ]);
    }

    public function saveNewProduct(array $storeData): Product
    {
        return $this->create($storeData);
    }


    public function updateProduct(array $updateData, int $id): Product
    {
        $product = $this->findOrFail($id);
        $product->update($updateData);
        return $product;
    }

    public function search(array $searchData): LengthAwarePaginator
    {
        return $this->model->withCategories()
            ->whereCategoryIds($searchData['category_ids'])
            ->whereId($searchData['id'])
            ->whereUserId($searchData['user_id'])
            ->whereName($searchData['name'])
            ->whereDescription($searchData['description'])
            ->paginate($searchData['perPage']);
    }

    public function destroy(int $id): Product
    {
        $product = $this->findOrFail($id);
        $product->delete();
        return $product;
    }
}
