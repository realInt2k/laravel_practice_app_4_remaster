<?php

namespace App\Http\Traits\ProductTraits;

trait ChecksProductMeta
{
    public function hasCategoryId(int $id): bool
    {
        return $this->categories()->where('id', $id)->count() > 0;
    }
}
