<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class DeleteRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_delete_role(): void
    {
        $role = Role::factory()->create();
        $roleCountBefore = Role::count();
        $response = $this->delete(route('roles.destroy', $role->id));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount($role->getTable(), $roleCountBefore)
            ->assertDatabaseHas($role->getTable(), ['id' => $role->id]);
    }

    /** @test */
    public function non_admin_cannot_delete_role(): void
    {
        $this->loginAsNewUser();
        $role = Role::factory()->create();
        $roleCountBefore = Role::count();
        $response = $this->delete(route('roles.destroy', $role->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
        $this->assertDatabaseCount($role->getTable(), $roleCountBefore)
            ->assertDatabaseHas($role->getTable(), ['id' => $role->id]);
    }

    /** @test */
    public function admin_cannot_delete_role(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $role = Role::factory()->create();
        $roleCountBefore = Role::count();
        $response = $this->from(route('roles.index'))
            ->delete(route('roles.destroy', $role->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
        $this->assertDatabaseCount($role->getTable(), $roleCountBefore)
            ->assertDatabaseHas($role->getTable(), ['id' => $role->id]);
    }

    /** @test */
    public function super_admin_can_delete(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $role = Role::factory()->create();
        $roleCountBefore = Role::count();
        $response = $this->from(route('roles.index'))
            ->delete(route('roles.destroy', $role->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertSessionMissing($this->getAuthErrorKey());
        $this->assertDatabaseCount('roles', $roleCountBefore - 1)
            ->assertDatabaseMissing('roles', $role->toArray());
    }

    /** @test */
    public function cannot_delete_role_if_uri_id_is_invalid(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $id = -1;
        $roleCountBefore = Role::count();
        $response = $this->delete(route('roles.destroy', $id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertDatabaseCount('roles', $roleCountBefore);
    }
}
