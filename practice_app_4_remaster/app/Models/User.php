<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'location',
        'phone',
        'about',
        'password_confirmation'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission', 'user_id', 'permission_id');
    }

    public function hasRoleId(int $roleId): bool
    {
        return $this->roles->where('id', $roleId)->count() > 0;
    }

    public function hasPermissionId(int $permId): bool
    {
        return $this->permissions->where('id', $permId)->count() > 0;
    }

    /**
     * check if user has all the role names or not
     * @explainParam $roleNames: string or array (i.e: "role1|role2" or [role1, role2])
     * @return bool
     */
    public function hasRoleNames(array |string $roleNames): bool
    {
        if (is_string($roleNames)) {
            $roleNames = explode('|', $roleNames);
        }
        $roleNameCountCheck = $this->roles()->wherein('name', $roleNames)->count();
        return $roleNameCountCheck === count($roleNames);
    }

    /**
     * check if user has all the permission names or not
     * @explainParam $permissionNames: string or array (i.e: "perm1|perm2" or [perm1, perm2])
     * @return bool
     */
    public function hasPermissionNames(array | string $permissionNames): bool
    {
        if (is_string($permissionNames)) {
            $permissionNames = explode('|', $permissionNames);
        }
        $result = true;
        foreach ($permissionNames as $name) {
            $indirectPermissionCountCheck = $this->roles()->whereRelation('permissions', 'name', $name)->count();
            $directPermissionCountCheck = $this->permissions()->where('name', $name)->count();
            if ($indirectPermissionCountCheck + $directPermissionCountCheck <= 0) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * This function is used for access-related controls
     */
    public function hasPermission(string $name): bool
    {
        return $this->isSuperAdmin() || $this->hasPermissionNames($name);
    }

    /**
     * This function is used for access-related controls
     */
    public function hasRole(string $name): bool
    {
        return $this->isSuperAdmin() || $this->hasRoleNames($name);
    }

    public function getAllPermissionNames(): array
    {
        $result = $this->permissions->pluck('name')->toArray();
        foreach ($this->roles as $role) {
            $result = array_merge($result, $role->permissions->pluck('name')->toArray());
        }
        return array_unique($result);
    }

    public function scopeWithRolesAndPermissions(Builder $query): Builder|null
    {
        return $query->with('roles')->with('permissions');
    }

    public function scopeWherePermissionName(Builder $query, string|null $name): Builder|null
    {
        return $name ?
            $query->whereRelation('permissions', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereRoleName(Builder $query, string|null $name): Builder|null
    {
        return $name ?
            $query->whereRelation('roles', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereId(Builder $query, int $id): Builder|null
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName(Builder $query, string|null $name): Builder|null
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereEmail(Builder $query, string|null $email): Builder|null
    {
        return $email ? $query->where('email', 'like', '%' . $email . '%') : null;
    }

    public function syncRoles(array $roleIds): array
    {
        return $this->roles()->sync($roleIds);
    }

    public function syncPermissions(array $permissionIds): array
    {
        return $this->permissions()->sync($permissionIds);
    }

    public function hasProduct(int $id): bool
    {
        return $this->products()->where('id', $id)->count() > 0;
    }

    public function isAdmin(): bool
    {
        return $this->hasRoleNames('admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRoleNames('super-admin');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'udpated_at' => 'datetime'
    ];
}
