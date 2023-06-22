<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Services\UserRolePermissionUtility;


class RoleSeeder extends Seeder
{
    public function createRoleIfNotExist($roleName)
    {
        if (Role::where('name', $roleName)->count() === 0) {
            return Role::create(['name' => $roleName]);
        } else {
            return Role::where(['name', $roleName])->first();
        }
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPermissions = [
            'users.store', 'users.update', 'users.destroy',
            'products.store', 'products.update', 'products.destroy',
            'roles.store', 'roles.update', 'roles.destroy'
        ];

        // admin
        $adminRole = $this->createRoleIfNotExist('admin');

        // user-manager
        $userManager = $this->createRoleIfNotExist('user-manager');
        for ($i = 0; $i < 3; $i++) {
            UserRolePermissionUtility::assignRoleWithPermissionName($userManager, $defaultPermissions[$i]);
        }

        // product-manager
        $productManager = $this->createRoleIfNotExist('product-manager');
        for ($i = 3; $i < 6; $i++) {
            UserRolePermissionUtility::assignRoleWithPermissionName($productManager, $defaultPermissions[$i]);
        }

        // role-manager
        $roleManager = $this->createRoleIfNotExist('role-manager');
        for ($i = 6; $i < 9; $i++) {
            UserRolePermissionUtility::assignRoleWithPermissionName($roleManager, $defaultPermissions[$i]);
        }
    }
}
