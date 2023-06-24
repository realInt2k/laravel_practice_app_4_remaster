<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Traits\ToArrayCorrectTimeZone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    public function scopeOnlyUserId(Builder $query, int $id): Builder|null
    {
        return $query->where('user_id', $id);
    }

    public function hasCategoryId(int $id): bool
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

    public function scopeWithCategories(Builder $query): Builder|null
    {
        return $query->with('categories');
    }

    public function scopeWhereCategoryName(Builder $query, string|null $categoryName): Builder|null
    {
        return $categoryName ?
            $query->whererelation('categories', 'name', 'like', '%' . $categoryName . '%') : null;
    }

    public function scopeWhereId(Builder $query, int|null $id): Builder|null
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereUserId(Builder $query, int|null $userId): Builder|null
    {
        return $userId ? $query->where('user_id', $userId) : null;
    }

    public function scopeWhereName(Builder $query, string|null $name): Builder|null
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereDescription(Builder $query, string|null $description): Builder|null
    {
        return $description ? $query->where('description', 'like', '%' . $description . '%') : null;
    }

    public function scopeWithName(Builder $query, string|null $name): Builder|null
    {
        return $name ? $query->where('name', 'like', "%$name%") : null;
    }

    public function scopeWhereCategoryIds(Builder $query, int|null $ids): Builder|null
    {
        return $ids ? $query->whereHas('categories', fn ($query) =>
        $query->wherein('id', $ids)) :
            null;
    }

    public function syncCategories(array $categoryIds): array
    {
        return $this->categories()->sync($categoryIds);
    }
}
