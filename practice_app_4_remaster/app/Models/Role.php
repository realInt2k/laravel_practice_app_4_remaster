<?php

namespace App\Models;

use App\Http\Traits\RoleTraits\ChecksRoleMeta;
use App\Http\Traits\RoleTraits\SetsRoleMeta;
use App\Http\Traits\ToArrayCorrectTimeZone;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static RoleFactory factory($count = null, $state = [])
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role wherePermissionName(?string $permissionName)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role withPermissions()
 * @mixin Builder
 */
class Role extends Model
{
    use HasFactory, ToArrayCorrectTimeZone, ChecksRoleMeta, SetsRoleMeta;

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

    public function scopeWithPermissions(Builder $query): Builder|null
    {
        return $query->with('permissions');
    }

    public function scopeWherePermissionName(Builder $query, string|null $permissionName): Builder|null
    {
        return $permissionName ?
            $query->whereHas('permissions', fn($query) => $query->where('name', 'like', '%' . $permissionName . '%')) :
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
