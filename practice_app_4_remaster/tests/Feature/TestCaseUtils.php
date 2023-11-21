<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use App\Services\UserRolePermissionUtility;

abstract class TestCaseUtils extends TestCase
{
    public function getAdminRole(): string
    {
        return config('app.aliases.admin_role');
    }

    public function getSuperAdminRole(): string
    {
        return config('app.aliases.super_admin_role');
    }

    public function getAuthErrorKey(): string
    {
        return config('app.aliases.auth_error_key');
    }

    protected function createNewUserWithRoleAndPermission(string $roleName, string $permissionName): User
    {
        $user = User::factory()->create();
        UserRolePermissionUtility::assignUserWithRoleName($user, $roleName);
        UserRolePermissionUtility::assignUserWithPermissionName($user, $permissionName);
        return $user;
    }

    protected function createNewUserWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        UserRolePermissionUtility::assignUserWithRoleName($user, $roleName);
        return $user;
    }

    protected function createNewUser(): User
    {
        return User::factory()->create();
    }

    protected function loginAsNewUserWithRoleAndPermission(string $roleName, string $permissionName): User
    {
        $user = $this->createNewUserWithRoleAndPermission($roleName, $permissionName);
        $this->actingAs($user);
        return $user;
    }

    protected function loginAsNewUserWithRole(string $roleName): User
    {
        $user = $this->createNewUserWithRole($roleName);
        $this->actingAs($user);
        return $user;
    }

    protected function loginAsNewUser(): User
    {
        $user = $this->createNewUser();
        $this->actingAs($user);
        return $user;
    }
}
