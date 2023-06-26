<?php

namespace App\Rules;

use Closure;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;

class NoCircularCategories implements ValidationRule
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // value is parent_id;
        $category = Category::findOrFail($this->id);
        $allChildIds = $category->getAllDescendantIds();
        $categoryNames = Category::wherein('id', $allChildIds)->pluck('name')->toArray();
        $strChildNames = '';
        $count = count($categoryNames);
        $i = 0;
        foreach ($categoryNames as $name) {
            if ($i < $count - 1) {
                $strChildNames .= $name . ", ";
            } else {
                $strChildNames .= $name;
            }
            $i++;
        }
        if (in_array($value, $allChildIds)) {
            $fail(
                'Cannot create circular category dependency. This category already includes: '
                    . $strChildNames
            );
        }
    }
}
