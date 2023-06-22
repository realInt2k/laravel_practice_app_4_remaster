<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use App\Services\UserRolePermissionUtility;

abstract class AbstractMiddlewareTestCase extends TestCase
{
    protected function deleteGetRedirectPage($request)
    {
        $redirectPage = $request['curPage'];
        if ($request['count'] == 1 && $request['curPage'] == $request['lastPage'] && $request['curPage'] != 1) {
            $redirectPage = $request['curPage'] - 1;
        }
        return $redirectPage;
    }

    protected function deleteGetRedirectUrl($request)
    {
        $redirectPage = $this->deleteGetRedirectPage($request);
        $href = $request['href'];
        if ($href === route('users.index') || $href === route('products.index') || $href === route('roles.index')) {
            $url = $href . "?&page=" . $redirectPage;
        } else {
            $url = $href . "&page=" . $redirectPage;
        }
        return $url;
    }

    protected function getTestingPermission()
    {
        if (count(Permission::where('name', 'permission to be sad')->get()) === 0) {
            $permission = Permission::create(['name' => 'permission to be sad']);
        } else {
            $permission = Permission::where('name', 'permission to be sad')->get()->first();
        }
        return $permission;
    }

    protected function testAsUser()
    {
        if (User::count() > 0) {
            $user = User::first();
        } else {
            $user = User::factory()->create();
        }
        $this->actingAs($user);
        return $user;
    }

    protected function testAsNewUserWithRolePermission(string $roleName, string $permissionName)
    {
        $user = User::factory()->create();
        UserRolePermissionUtility::assignUserWithRoleName($user, $roleName);
        $role = UserRolePermissionUtility::getRoleFromName($roleName);
        UserRolePermissionUtility::assignUserWithPermissionName($user, $permissionName);
        $this->actingAs($user);
        return $user;
    }

    protected function testAsNewUserWithRolePermissions(string $roleName, array $permissionNames)
    {
        $user = User::factory()->create();
        UserRolePermissionUtility::assignUserWithRoleName($user, $roleName);
        $role = UserRolePermissionUtility::getRoleFromName($roleName);
        foreach ($permissionNames as $permissionName) {
            UserRolePermissionUtility::assignUserWithPermissionName($user, $permissionName);
        }
        $this->actingAs($user);
        return $user;
    }

    protected function testAsNewUserWithSuperAdmin()
    {
        $user = User::factory()->create();
        UserRolePermissionUtility::assignUserWithRoleName($user, 'super-admin');
        $this->actingAs($user);
        return $user;
    }

    protected function testAsUserWithSuperAdmin()
    {
        if (User::count() > 0) {
            $user = User::first();
        } else {
            $user = User::factory()->create();
        }
        UserRolePermissionUtility::assignUserWithRoleName($user, 'super-admin');
        $this->actingAs($user);
        return $user;
    }

    protected function testAsNewUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
}
