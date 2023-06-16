<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'parent_id'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }

    public function scopeOfUserId($query, $id)
    {
        $query->wherein("id", Product::onlyUserId($id)->pluck('category_id')->toArray());
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function hasChildId(int $id)
    {
        return $this->children()->where('id', $id)->count() > 0;
    }

    public function getAllChildIds(): array
    {
        $childIds = [];
        if (count($this->children) > 0) {
            foreach ($this->children as $cat) {
                $childIds = array_merge($childIds, $cat->getAllChildIds());
            }
        }
        $childIds[] = $this->id;
        return $childIds;
    }

    public function scopeWhereName($query, $name)
    {
        return $name != null ? $query->where('name', 'like', '%' . $name . '%') : null;
    }
}
