<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Traits\ToArrayCorrectTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role wherePermissionName(?string $permissionName)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role withPermissions()
 * @mixin \Eloquent
 */
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

    public function scopeWherePermissionName(Builder $query, string|null $permissionName): Builder|null
    {
        return $permissionName ?
            $query->whereHas('permissions', fn ($query) => $query->where('name', 'like', '%' . $permissionName . '%')) :
            null;
    }

    public function scopeWhereId(Builder $query, int|null $id): Builder|null
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName(Builder $query, string|null $name): Builder|null
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }
}
