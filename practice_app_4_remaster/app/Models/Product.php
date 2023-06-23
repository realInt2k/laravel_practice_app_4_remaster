<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ToArrayCorrectTimeZone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, ToArrayCorrectTimeZone;

    const IMAGE_PATH = 'storage/images/';
    const DEFAULT_IMAGE = 'storage/defaultImages/noImage.png';

    protected $table = 'products';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    public function scopeOnlyUserId($query, $id)
    {
        $query->where('user_id', $id);
    }

    public function hasCategoryId($id)
    {
        return $this->categories()->where('id', $id)->count() > 0;
    }

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value, array $attributes) =>
            $attributes['image'] && Storage::disk('public')->exists('images/' . $attributes['image']) ?
                self::IMAGE_PATH . $attributes['image']
                : self::DEFAULT_IMAGE
        );
    }

    public function scopeWithCategories($query)
    {
        return $query->with('categories');
    }

    public function scopeWhereCategoryName($query, $categoryName)
    {
        return $categoryName ?
            $query->whererelation('categories', 'name', 'like', '%' . $categoryName . '%') : null;
    }

    public function scopeWhereId($query, $id)
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereUserId($query, $userId)
    {
        return $userId ? $query->where('user_id', $userId) : null;
    }

    public function scopeWhereName($query, $name)
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereDescription($query, $description)
    {
        return $description ? $query->where('description', 'like', '%' . $description . '%') : null;
    }

    public function scopeWithName($query, $name)
    {
        return $name ? $query->where('name', 'like', "%$name%") : null;
    }

    public function scopeWhereCategoryIds($query, $ids)
    {
        return $ids ? $query->whereHas('categories', fn ($query) =>
        $query->wherein('id', $ids)) :
            null;
    }

    public function syncCategories($categoryIds)
    {
        return $this->categories()->sync($categoryIds);
    }
}
