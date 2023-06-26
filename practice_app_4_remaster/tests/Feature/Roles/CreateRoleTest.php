<?php

namespace Tests\Feature\Roles;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class CreateRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_create_role_form(): void
    {
        $response = $this->get(route('roles.create'));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_cannot_see_create_role_form(): void
    {
        $this->loginAsNewUser();
        $this->try_to_access_then_be_redirected();
    }

    /** @test */
    public function admin_cannot_see_create_role_form(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_access_then_be_redirected();
    }

    /** @test */
    public function super_admin_can_see_create_role_form(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $response = $this->from(route('users.profile'))->get(route('roles.create'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertSessionMissing($this->getAuthErrorKey())
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data', fn($data)
                    => !empty($data) && str_contains($data, 'name') && str_contains($data, 'permissions')
                )
                ->etc());
    }

    public function try_to_access_then_be_redirected(): void
    {
        $response = $this->from(route('users.profile'))->get(route('roles.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors(config('constants.AUTHENTICATION_ERROR_KEY'))
            ->assertRedirect(route('users.profile'));
    }
}
