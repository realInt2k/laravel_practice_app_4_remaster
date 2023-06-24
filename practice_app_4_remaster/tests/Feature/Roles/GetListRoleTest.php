<?php

namespace Tests\Feature\Roles;

use Illuminate\Http\Response;
use Tests\Feature\TestCaseUtils;
use Illuminate\Testing\Fluent\AssertableJson;

class GetListRoleTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_get_role_list(): void
    {
        $response = $this->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_see_role_list_as_non_admin_user(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /** @test */
    public function cannot_see_role_list_as_admin_only_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission(config('custom.aliases.admin_role'), 'roles.store');
        $response = $this->from(route('users.profile'))->get(route('roles.index'));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('users.profile'));
    }

    /** @test */
    public function can_see_role_list_as_super_admin_user(): void
    {
        $this->loginAsNewUserWithRole(config('custom.aliases.super_admin_role'));
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
