<?php

namespace Tests\Feature\Users;

use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class GetUserListTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_user_list(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function admin_can_see_user_list(): void
    {
        $this->withoutExceptionHandling();
        $this->testAsNewUserWithRolePermission('admin', 'reasoning');
        $response = $this->get(route('users.index'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('pages.users.index');
        $response->assertViewHas('roles');
    }

    /**
     * @test
     */
    public function non_admin_can_see_user_list(): void
    {
        $this->withoutExceptionHandling();
        $this->testAsNewUser();
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('pages.users.index');
        $response->assertViewHas('roles');
    }
}
