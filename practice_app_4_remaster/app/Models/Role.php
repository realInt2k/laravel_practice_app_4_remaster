<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Traits\ToArrayCorrectTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory, ToArrayCorrectTimeZone;
    protected $table = 'roles';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function hasPermissionId(int $id): bool
    {
        return $this->permissions->find($id) !== null;
    }

    public function syncPermissionIds(array $permissionIds): array
    {
        return $this->permissions()->sync($permissionIds);
    }

    public function scopeWithPermissions(Builder $query): Builder|null
    {
        return $query->with('permissions');
    }

    public function scopeWherePermissionName(Builder $query, $permissionName): Builder|null
    {
        return $permissionName ?
            $query->whereHas('permissions', fn ($query) => $query->where('name', 'like', '%' . $permissionName . '%')) :
            null;
    }

    public function scopeWhereId(Builder $query, int $id): Builder|null
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName(Builder $query, string $name): Builder|null
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }
}
