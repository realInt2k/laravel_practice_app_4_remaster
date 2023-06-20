<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\Feature\AbstractMiddlewareTestCase;

class DeleteRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_delete_role(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(1, 3, 3, route('roles.index'), 3);
            $id = rand(0, Role::count());
            $roleCountBefore = Role::count();
            $response = $this->delete(route('roles.destroy', $id), $requestData);
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
            $this->assertDatabaseCount('roles', $roleCountBefore);
        });
    }

    /** @test */
    public function authenticated_without_roles_destroy_permission_cannot_delete(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(1, 3, 3, route('roles.index'), 3);
            $this->testAsNewUser();
            $roleCountBefore = Role::count();
            $id = rand(0, Role::count());
            $response = $this->delete(route('roles.destroy', $id), $requestData);
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.authenticationErrorKey'));
            $this->assertDatabaseCount('roles', $roleCountBefore);
        });
    }

    /** @test */
    public function admin_with_roles_destroy_permission_cannot_delete(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('admin', 'roles-destroy');
            $role = Role::factory()->create();
            $roleCountBefore = Role::count();
            $response = $this->from(route('roles.index'))->delete(route('roles.destroy', $role->id));
            $this->assertDatabaseHas('roles', $role->toArray());
            $this->assertDatabaseCount('roles', $roleCountBefore);
        });
    }

    /** @test */
    public function super_admin_can_delete(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(1, 3, 3, route('roles.index'), 3);
            $this->testAsUserWithSuperAdmin();
            $role = Role::factory()->create();
            $roleCountBefore = Role::count();
            $response = $this->from(route('roles.index'))->delete(route('roles.destroy', $role->id), $requestData);
            $this->assertDatabaseMissing('roles', $role->toArray());
            $this->assertDatabaseCount('roles', $roleCountBefore - 1);
        });
    }

    /** @test */
    public function cannot_delete_role_if_uri_id_is_invalid(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(1, 3, 3, route('roles.index'), 3);
            $this->testAsUserWithSuperAdmin();
            $roleCountBefore = Role::count();
            $id = -1;
            $response = $this->delete(route('roles.destroy', $id), $requestData);
            $response->assertStatus(Response::HTTP_NOT_FOUND);
            $this->assertDatabaseCount('roles', $roleCountBefore);
        });
    }
}
