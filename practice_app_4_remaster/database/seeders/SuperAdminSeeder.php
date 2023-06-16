<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'int2k@gmail.com')->count() === 0) {
            $user = User::create([
                'name' => 'int2k',
                'email' => 'int2k@gmail.com',
                'password' => 'int2k'
            ]);
        } else {
            $user = User::where('email', 'int2k@gmail.com')->first();
        }

        if (Role::where('name', 'super-admin')->count() === 0) {
            $role = Role::create(['name' => 'super-admin']);
        } else {
            $role = Role::where('name', 'super-admin')->first();
        }
        $role->permissions()->sync([]);
        $user->roles()->sync($role->id);
    }
}
