<?php

namespace App\Http\Traits\ProductTraits;

trait SetsProductMeta
{
    public function syncCategories(array $categoryIds): array
    {
        return $this->categories()->sync($categoryIds);
    }
}
