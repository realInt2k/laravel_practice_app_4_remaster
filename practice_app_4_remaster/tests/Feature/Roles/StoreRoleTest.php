<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;
use Webmozart\Assert\Assert;

class StoreRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_store_role(): void
    {
        $numberOfRolesBefore = Role::count();
        $data = Role::factory()->make();
        $response = $this->post(route('roles.store'), $data->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('roles', $numberOfRolesBefore);
    }

    /** @test */
    public function non_admin_cannot_store_role(): void
    {
        $this->loginAsNewUser();
        $data = Role::factory()->make();
        $numberOfRolesBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $data->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
        $this->assertDatabaseCount('roles', $numberOfRolesBefore);
    }

    /** @test */
    public function admin_cannot_store_role(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $data = Role::factory()->make();
        $numberOfRolesBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $data->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
        $this->assertDatabaseCount('roles', $numberOfRolesBefore);
    }

    /** @test */
    public function storing_role_requires_validation(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $numberOfRolesBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors('name');
        $this->assertDatabaseCount('roles', $numberOfRolesBefore);
    }

    /** @test */
    public function super_admin_can_store_role(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $permission = Permission::factory()->create();
        $newRole = Role::factory()->make();
        $numberOfRolesBefore = Role::count();
        $storeData = array_merge($newRole->toArray(), ['permissions' => [$permission->id]]);
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $storeData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', fn (AssertableJson $json) => $json
                    ->where('name', $newRole->name)
                    ->etc()
                )
                ->etc()
            );
        $this->assertDatabaseCount('roles', $numberOfRolesBefore + 1)
            ->assertDatabaseHas('roles', $newRole->toArray())
            ->assertDatabaseHas('role_permission', [
            'role_id' => Role::where('name', $newRole->name)->first()->id,
            'permission_id' => Permission::where('name', $permission->name)->first()->id
        ]);
    }

    public function invalid_role_name_test(string|int $name): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $storeData = Role::factory()->make();
        $storeData->name = $name;
        $numberOfRoleBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $storeData->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors(['name'])
            ->assertRedirect(route('roles.create'));
        $this->assertDatabaseCount('roles', $numberOfRoleBefore);
    }

    /** @test */
    public function cannot_store_role_with_non_string_name(): void
    {
        $this->invalid_role_name_test(123);
    }

    /** @test */
    public function cannot_store_role_with_name_length_smaller_than_3(): void
    {
        $this->invalid_role_name_test(Str::random(2));
    }

    /** @test */
    public function cannot_store_role_with_duplicated_name(): void
    {
        $existingName = Role::first()->name;
        $this->invalid_role_name_test($existingName);
    }
}
