<?php

namespace Tests\Feature\Roles;

use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;
use Illuminate\Testing\Fluent\AssertableJson;

class GetListRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_get_role_list(): void
    {
        $response = $this->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_see_role_list_as_non_admin(): void
    {
        $this->loginAsNewUser();
        $response = $this->from(route('users.profile'))->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey())
            ->assertRedirect(route('users.profile'));
    }

    /** @test */
    public function cannot_see_role_list_as_admin(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'roles.store');
        $response = $this->from(route('users.profile'))->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey())
            ->assertRedirect(route('users.profile'));
    }

    /** @test */
    public function can_see_role_list_as_super_admin(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $response = $this->from(route('users.profile'))->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertViewIs('pages.roles.index');
        $response = $this->from(route('roles.index'))->get(route('roles.search'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where(
                        'data',
                        fn ($data) =>
                        !empty($data)
                            && str_contains($data, 'ID')
                            && str_contains($data, 'NAME')
                            && str_contains($data, 'PERMISSIONS')
                    )
                    ->etc()
            );
    }
}
