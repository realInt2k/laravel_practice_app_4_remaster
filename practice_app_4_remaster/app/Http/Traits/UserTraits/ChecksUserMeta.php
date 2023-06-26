<?php

namespace App\Http\Traits\UserTraits;

trait ChecksUserMeta
{
    public function existsRoleId(int $roleId): bool
    {
        return $this->roles->where('id', $roleId)->count() > 0;
    }

    public function existsPermissionId(int $permId): bool
    {
        return $this->permissions->where('id', $permId)->count() > 0;
    }

    public function hasPermission(string $name): bool
    {
        return $this->isSuperAdmin() || $this->existsPermissionNames($name);
    }

    public function hasRole(string $name): bool
    {
        return $this->isSuperAdmin() || $this->existsRoleNames($name);
    }

    public function isSuperAdmin(): bool
    {
        return $this->existsRoleNames(config('custom.aliases.super_admin_role'));
    }

    public function isAdmin(): bool
    {
        return $this->existsRoleNames(config('custom.aliases.admin_role'));
    }

    /**
     * check whether permission names are connected to this user's entity or not
     * @explainParam $permissionNames: string or array (i.e: "perm1|perm2" or [perm1, perm2])
     * @param array|string $permissionNames
     * @return bool
     */
    public function existsPermissionNames(array|string $permissionNames): bool
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
     * check if the roles name are in this user entity
     * @explainParam $roleNames: string or array (i.e: "role1|role2" or [role1, role2])
     * @param array|string $roleNames
     * @return bool
     */
    public function existsRoleNames(array|string $roleNames): bool
    {
        if (is_string($roleNames)) {
            $roleNames = explode('|', $roleNames);
        }
        $roleNameCountCheck = $this->roles()->wherein('name', $roleNames)->count();
        return $roleNameCountCheck === count($roleNames);
    }

    public function ownProduct(int $id): bool
    {
        return $this->products()->where('id', $id)->count() > 0;
    }
}
