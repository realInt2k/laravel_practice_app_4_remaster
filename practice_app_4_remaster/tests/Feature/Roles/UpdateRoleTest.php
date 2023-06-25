<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class UpdateRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_update_role(): void
    {
        DB::transaction(function () {
            $role = Role::factory()->create();
            $response = $this->put(route('roles.update', $role->id));
            $response->assertStatus(Response::HTTP_FOUND)
                ->assertRedirect(route('login'));
        });
    }

    /** @test */
    public function non_admin_cannot_update_role(): void
    {
        DB::transaction(function () {
            $this->loginAsNewUser();
            $role = Role::factory()->create();
            $newRoleData = Role::factory()->make();
            $response = $this->put(route('roles.update', $role->id), $newRoleData->toArray());
            $response->assertStatus(Response::HTTP_FOUND)
                ->assertSessionHasErrors($this->getAuthErrorKey());
        });
    }

    /** @test */
    public function admin_cannot_update_role(): void
    {
        DB::transaction(function () {
            $this->loginAsNewUserWithRole($this->getAdminRole());
            $role = Role::factory()->create();
            $newRoleData = Role::factory()->make();
            $response = $this->put(route('roles.update', $role->id), $newRoleData->toArray());
            $response->assertStatus(Response::HTTP_FOUND)
                ->assertSessionHasErrors($this->getAuthErrorKey());
        });
    }

    /** @test */
    public function super_admin_can_update_role(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $role = Role::factory()->create();
        $newRole = Role::factory()->make();
        $response = $this->from(route('roles.edit', $role->id))
            ->put(route('roles.update', $role->id), $newRole->toArray());
        $this->assertDatabaseHas('roles', $newRole->toArray());
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'data',
                    fn(AssertableJson $json) => $json
                        ->where('id', $role->id)
                        ->where('name', $newRole->name)
                        ->etc()
                )
                ->etc()
        );
    }

    public function invalid_role_name_test(string|int $name, $roleId): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $updateData = Role::factory()->make();
        $updateData->name = $name;
        $response = $this->from(route('roles.edit', $roleId))
            ->put(route('roles.update', $roleId), $updateData->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors(['name'])
            ->assertRedirect(route('roles.edit', $roleId));
    }

    /** @test */
    public function cannot_update_role_with_non_string_name(): void
    {
        $role = Role::factory()->create();
        $this->invalid_role_name_test(123,  $role->id);
    }

    /** @test */
    public function cannot_update_role_with_name_length_smaller_than_3(): void
    {
        $role = Role::factory()->create();
        $this->invalid_role_name_test(Str::random(2),  $role->id);
    }

    /** @test */
    public function cannot_update_role_with_duplicated_name(): void
    {
        $existingName = Role::factory()->create()->name;
        $role = Role::factory()->create();
        $this->invalid_role_name_test($existingName, $role->id);
    }

    /** @test */
    public function cannot_update_role_with_invalid_id(): void
    {
        DB::transaction(function () {
            $this->loginAsNewUserWithRole($this->getSuperAdminRole());
            $id = -1;
            $data = ['name' => 'bullshit'];
            $response = $this->put(route('roles.update', $id), $data);
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }
}
