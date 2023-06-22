<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function createIfNotExist($permission)
    {
        if (Permission::where('name', $permission)->count() === 0) {
            DB::table('permissions')->insert(['name' => $permission]);
        }
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPermissions = [
            'users.store', 'users.update', 'users.destroy',
            'roles.store', 'roles.update', 'roles.destroy',
            'products.store', 'products.update', 'products.destroy',
            'categories.store', 'categories.update', 'categories.destroy'
        ];
        foreach ($defaultPermissions as $permission) {
            $this->createIfNotExist($permission);
        }
    }
}
