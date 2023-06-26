<?php

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    public function withRandomPermission(): RoleFactory
    {
        return $this->afterCreating(function (Role $role) {
            $permissionId = Permission::factory()->create()->id;
            $role->permissions()->attach($permissionId);
        });
    }

    public function withRandomPermissions(int $count): RoleFactory
    {
        return $this->afterCreating(function (Role $role) use ($count) {
            $permissionIds = Permission::factory($count)->create()->pluck('id')->toArray();
            $role->permissions()->attach($permissionIds);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->regexify('[A-Za-z0-9]{' . mt_rand(3, 10) . '}') . $this->faker->firstNameMale()
        ];
    }
}
