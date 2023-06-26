<?php

namespace Tests\Feature\Users;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class GetUserListTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_user_list(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_can_see_user_index_page(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('users.index'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertViewIs('pages.users.index')
            ->assertViewHas('roles');
    }

    /** @test */
    public function authenticated_can_get_user_list(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('users.search'));
        $response->assertStatus(Response::HTTP_OK);
        $response
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where(
                        'data',
                        fn ($data) =>
                            !empty($data)
                            && str_contains($data, 'ID')
                            && str_contains($data, 'NAME')
                            && str_contains($data, 'EMAIL')
                            && str_contains($data, 'ROLE')
                    )
                    ->etc()
            );
    }
}
