<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class EditRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_edit_role_form(): void
    {
        $id = random_int(0, Role::count());
        $response = $this->get(route('roles.edit', $id));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_cannot_see_edit_role_form(): void
    {
        $this->loginAsNewUser();
        $this->try_to_access_then_be_redirected();
    }

    /** @test */
    public function admin_cannot_see_edit_role_form(): void
    {
        $this->loginAsNewUserWithRole(config('custom.aliases.admin_role'));
        $this->try_to_access_then_be_redirected();
    }

    /** @test */
    public function super_admin_can_see_edit_role_form(): void
    {
        $this->loginAsNewUserWithRole(config('custom.aliases.super_admin_role'));
        $role = Role::factory()->withRandomPermission()->create();
        $response = $this->from(route('users.profile'))->get(route('roles.edit', $role->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
            ->where('data', fn($data)
                    => !empty($data)
                    && str_contains($data, $role->name)
                )->etc()
            )
            ->assertSee($role->permissions->pluck('name')->toArray());
    }

    /** @test */
    public function cannot_see_edit_role_form_with_invalid_id(): void
    {
        $this->loginAsNewUser();
        $id = -1;
        $response = $this->get(route('roles.edit', $id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function try_to_access_then_be_redirected(): void
    {
        $role = Role::factory()->create();
        $response = $this->from(route('users.profile'))->get(route('roles.edit', $role->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'))
            ->assertRedirect(route('users.profile'));
    }
}
