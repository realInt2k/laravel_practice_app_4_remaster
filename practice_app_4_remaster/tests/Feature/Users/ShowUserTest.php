<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\UserRolePermissionUtility;
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
}
