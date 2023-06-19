<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\UserRolePermissionUtility;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class ShowUserTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_a_user(): void
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function can_not_see_a_user_without_admin_roles(): void
    {
        $user = $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'knot');
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(302);
        $response->assertSessionHas(config("constants.authenticationErrorKey"));
    }

    /** @test */
    public function can_get_user_with_admin_roles(): void
    {
        $user = $this->testAsNewUserWithRolePermission('admin', 'perm' . Str::random(5));
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('html')->etc()
        );
        $response->assertSee([$user->name, $user->email]);
    }
}
