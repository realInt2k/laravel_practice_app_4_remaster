<?php

namespace App\Http\Traits\CategoryTraits;

trait GetsCategoryMeta
{
    public function getAllDescendantIds(): array
    {
        $childIds = [];
        if (count($this->children) > 0) {
            foreach ($this->children as $cat) {
                $childIds = array_merge($childIds, $cat->getAllDescendantIds());
            }
        }
        $childIds[] = $this->id;
        return $childIds;
    }
}
