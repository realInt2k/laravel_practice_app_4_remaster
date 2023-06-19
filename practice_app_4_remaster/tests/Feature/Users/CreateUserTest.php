<?php

namespace Tests\Feature\Users;

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class CreateUserTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_form_and_forced_login(): void
    {
        $response = $this->get($this->getRoute());
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function non_admin_cannot_create_user(): void
    {
        $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'play all day long');
        $response = $this->get($this->getRoute());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /**
     * @test
     */
    public function admin_without_permission_cannot_create_user(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'play all day long');
        $response = $this->get($this->getRoute());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /**
     * @test
     */
    public function authenticated_with_super_admin_privilege_can_see_create_user_form(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'users-store');
        $response = $this->get($this->getRoute());
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'html'
                )
                ->etc()
        );
    }

    public function getRoute()
    {
        return route('users.create');
    }
}
