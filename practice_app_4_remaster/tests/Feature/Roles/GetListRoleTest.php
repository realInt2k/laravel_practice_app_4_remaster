<?php

namespace Tests\Feature\Roles;

use Tests\Feature\AbstractMiddlewareTestCase;

class GetListRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_get_role_list(): void
    {
        $response = $this->get(route('roles.index'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function cannot_see_role_list_as_normal_user(): void
    {
        $this->withoutExceptionHandling();
        $this->testAsNewUser();
        $response = $this->get(route('roles.index'));
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /**
     * @test
     */
    public function cannot_see_role_list_as_admin(): void
    {
        $this->withoutExceptionHandling();
        $this->testAsNewUserWithRolePermission('admin', 'roles.store');
        $response = $this->from(route('users.profile'))->get(route('roles.index'));
        $response->assertStatus(302);
        $response->assertRedirect(route('users.profile'));
    }
}
