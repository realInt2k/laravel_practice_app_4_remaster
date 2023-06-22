<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Support\Str;
use Tests\Feature\AbstractMiddlewareTestCase;

class StoreRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_store_product(): void
    {
        $numberOfRolesBefore = Role::count();
        $data = Role::factory()->make();
        $response = $this->post(route('roles.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('roles', $numberOfRolesBefore);
    }

    /** @test */
    public function authenticated_cannot_store_role_without_roles_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('impersonators' . Str::random(10), 'roles-impersonating');
        $data = Role::factory()->make();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /** @test */
    public function can_store_role_with_roles_store_permission_and_valid_data(): void
    {
        $user = $this->testAsNewUserWithRolePermission('super-admin', 'roles.store');
        $permission = $this->getTestingPermission();
        $data = Role::factory()->make();
        $numberOfRoleBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), array_merge($data->toArray(), ['permissions' => [$permission->id]]));
        $response->assertStatus(200);
        $this->assertDatabaseCount('roles', $numberOfRoleBefore + 1);
        $this->assertDatabaseHas('roles', $data->toArray());
    }

    /** @test */
    public function authenticated_can_store_role_as_super_admin_and_valid_data(): void
    {
        $user = $this->testAsUserWithSuperAdmin();
        $permission = $this->getTestingPermission();
        $data = Role::factory()->make();
        $numberOfRoleBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), array_merge($data->toArray(), ['permissions' => [$permission->id]]));
        $response->assertStatus(200);
        $this->assertDatabaseCount('roles', $numberOfRoleBefore + 1);
        $this->assertDatabaseHas('roles', $data->toArray());
    }

    /** @test */
    public function authenticated_cannot_store_role_with_roles_store_permission_and_invalid_name_as_number(): void
    {
        $user = $this->testAsNewUserWithRolePermission('super-admin', 'roles.store');
        $data = Role::factory()->make();
        $data->name = 123;
        $numberOfRoleBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $data->toArray());
        $response->assertStatus(302);
        $this->assertDatabaseCount('roles', $numberOfRoleBefore);
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function authenticated_cannot_store_role_with_roles_store_permission_and_invalid_name_length_smaller_than_3(): void
    {
        // $this->withoutExceptionHandling();
        $user = $this->testAsNewUserWithRolePermission('super-admin', 'roles.store');
        $data = Role::factory()->make();
        $data->name = 'a';
        $numberOfRoleBefore = Role::count();
        $response = $this->from(route('roles.create'))
            ->post(route('roles.store'), $data->toArray());
        $response->assertStatus(302);
        $this->assertDatabaseCount('roles', $numberOfRoleBefore);
        $response->assertSessionHasErrors(['name']);
    }
}
