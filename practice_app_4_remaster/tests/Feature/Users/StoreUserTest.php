<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Feature\AbstractMiddlewareTestCase;

class StoreUserTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_ofcourse_can_not_store(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $response = $this->post(route('users.store'), $user->toArray());
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /**
     * @test
     */
    public function authenticated_with_super_admin_privilege_can_store(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithSuperAdmin();
            $user = User::factory()->make();
            $userWithPassword = $user->toArray();
            $userWithPassword['password'] = $user->password;
            $userWithPassword['roles'] = [];
            $userWithPassword['permissions'] = [];
            $countBefore = User::count();
            $response = $this->from(route('users.create'))->post(route('users.store'), $userWithPassword);
            $countAfter = User::count();
            $response->assertStatus(200);
            $this->assertEquals($countBefore + 1, $countAfter);
        });
    }

    /**
     * @test
     */
    public function authenticated_with_users_store_permission_and_admin_privilege_can_store(): void
    {
        DB::transaction(function () {
            $user = $this->testAsNewUserWithRolePermissions('admin', ['users-store']);
            $user = User::factory()->make();
            $userWithPassword = $user->toArray();
            $userWithPassword['password'] = $user->password;
            $userWithPassword['roles'] = [];
            $userWithPassword['permissions'] = [];
            $countBefore = User::count();
            $response = $this->from(route('users.create'))->post(route('users.store'), $userWithPassword);
            $response->assertSessionDoesntHaveErrors();
            $countAfter = User::count();
            $response->assertStatus(200);
            $this->assertEquals($countBefore + 1, $countAfter);
        });
    }

    /** @test */
    public function cannot_store_user_with_duplication_email(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUserWithSuperAdmin();
            $toBeEditedUser = User::factory()->make();
            $toBeEditedUserWithPassword = $toBeEditedUser->toArray();
            $toBeEditedUserWithPassword['password'] = $toBeEditedUser->password;
            $toBeEditedUserWithPassword['email'] = $currentUser->email;
            $countBefore = User::count();
            $response = $this->from(route('users.create'))
                ->post(route('users.store'), $toBeEditedUserWithPassword);
            $countAfter = User::count();
            $response->assertStatus(302);
            $response->assertSessionHasErrors(['email']);
            $response->assertRedirect(route('users.create'));
            $this->assertEquals($countBefore, $countAfter);
        });
    }
}
