<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;

class UserRolePermissionUtility
{
    public static function getAllRoleIdsOfUser(User $user)
    {
        return $user->roles()->pluck('id')->toArray();
    }

    public static function checkIfUserHasRoleName(User $user, string $roleName)
    {
        $roleIdsOfUser = UserRolePermissionUtility::getAllRoleIdsOfUser($user);
        $roleNameCountCheck = Role::wherein('id', $roleIdsOfUser)
            ->where('name', '=', $roleName)
            ->count();
        return $roleNameCountCheck > 0;
    }

    public static function getRoleFromName(string $roleName)
    {
        return Role::where('name', '=', $roleName)->first();
    }

    public static function getPermissionFromName(string $permissionName)
    {
        return Permission::where('name', '=', $permissionName)->first();
    }

    public static function assignUserWithRoleName(User &$user, string $roleName)
    {
        $userHasRole = UserRolePermissionUtility::checkIfUserHasRoleName($user, $roleName);
        if (!$userHasRole) {
            $role = UserRolePermissionUtility::getRoleFromName($roleName);
            if ($role === null) {
                $role = Role::create([
                    'name' => $roleName
                ]);
            }
            $user->syncRoles([$role->id]);
            $permissionIds = $role->permissions->pluck('id')->toArray();
            $user->syncPermissions($permissionIds);
        }
    }

    /**
     * assign Role role with a permission
     */
    public static function assignRoleWithPermissionName(Role &$role, string $permissionName)
    {
        if ($role->permissions->where('name', $permissionName)->count() > 0) {
            return;
        } else {
            if (Permission::where('name', $permissionName)->count() > 0) {
                $permission = Permission::where('name', $permissionName)->first();
            } else {
                $permission = Permission::create(['name' => $permissionName]);
            }
            $role->permissions()->attach($permission->id);
        }
    }

    /**
     * assign Role role with a permission
     */
    public static function assignUserWithPermissionName(User &$user, string $permissionName)
    {
        if (!$user->existsPermissionNames($permissionName)) {
            $permission = UserRolePermissionUtility::getPermissionFromName($permissionName);
            if ($permission === null) {
                $permission = Permission::create([
                    'name' => $permissionName
                ]);
            }
            $user->syncPermissions([$permission->id]);
        }
    }
}
