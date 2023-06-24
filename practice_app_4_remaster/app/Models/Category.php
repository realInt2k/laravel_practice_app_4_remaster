<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'parent_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeOfUserId(Builder $query, int $id)
    {
        return $query->wherein("id", Product::onlyUserId($id)->pluck('category_id')->toArray());
    }

    /**
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeWhereName(Builder $query, string|null $name)
    {
        return $name != null ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

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
