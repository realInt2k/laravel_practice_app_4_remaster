<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;

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

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission', 'user_id', 'permission_id');
    }

    public function hasRoleId($roleId): bool
    {
        return $this->roles->where('id', $roleId)->count() > 0;
    }

    public function hasPermissionId($permId): bool
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

    public function scopeWithRolesAndPermissions($query)
    {
        $query->with('roles')->with('permissions');
    }

    public function scopeWherePermissionName($query, $name)
    {
        return $name ?
            $query->whereRelation('permissions', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereRoleName($query, $name)
    {
        return $name ?
            $query->whereRelation('roles', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereId($query, $id)
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName($query, $name)
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereEmail($query, $email)
    {
        return $email ? $query->where('email', 'like', '%' . $email . '%') : null;
    }

    public function syncRoles($roleIds)
    {
        return $this->roles()->sync($roleIds);
    }

    public function syncPermissions($permissionIds)
    {
        return $this->permissions()->sync($permissionIds);
    }

    public function hasProduct(int $id)
    {
        return $this->products()->where('id', $id)->count() > 0;
    }

    public function isAdmin()
    {
        return $this->hasRoleNames('admin');
    }

    public function isSuperAdmin()
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
