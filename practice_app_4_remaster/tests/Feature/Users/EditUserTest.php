<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class EditUserTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_edit_form(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $response = $this->get($this->getRoute($user->id));
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /**
     * @test
     */
    public function authenticated_but_cannot_edit_because_not_admins(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $this->testAsNewUser();
            $response = $this->get($this->getRoute($user->id));
            $response->assertSessionHas(config('constants.authenticationErrorKey'));
            $response->assertStatus(302);
        });
    }

    /**
     * @test
     */
    public function authenticated_can_see_user_profile(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $this->actingAs($user);
            $response = $this->get(route('users.profile'));
            $response->assertStatus(200);
            $response->assertViewIs('pages.users.user-profile');
        });
    }

    /**
     * @test
     */
    public function authenticated_can_edit_with_super_admin_privilege(): void
    {
        DB::transaction(function () {
            /** @var User */
            $user = $this->testAsNewUserWithRolePermission('role' . Str::random(10), 'perm' . Str::random(10));
            $this->testAsUserWithSuperAdmin();
            $response = $this->get($this->getRoute($user->id));
            $response->assertStatus(200);
            $response->assertJson(
                fn (AssertableJson $json) => $json
                ->has(
                    'html'
                )
                ->etc()
            );
            $response->assertSee($user->name);
            $response->assertSee($user->email);
            $response->assertSee($user->permissions()->pluck('name')->toArray());
            $response->assertSee($user->roles()->pluck('name')->toArray());
        });
    }

    /**
     * @test
     */
    public function authenticated_can_edit_with_admin_privilege_and_permission(): void
    {
        DB::transaction(function () {
            /** @var User */
            $user = $this->testAsNewUserWithRolePermission('role' . Str::random(10), 'perm' . Str::random(10));
            $this->testAsNewUserWithRolePermission('admin', 'users-update');
            $response = $this->get($this->getRoute($user->id));
            $response->assertStatus(200);
            $response->assertJson(
                fn (AssertableJson $json) => $json
                ->has(
                    'html'
                )
                ->etc()
            );
            $response->assertSee($user->name);
            $response->assertSee($user->email);
            $response->assertDontSee($user->permissions()->pluck('name')->toArray());
            $response->assertDontSee($user->roles()->pluck('name')->toArray());
        });
    }

    /**
     * @test
     */
    public function cannot_edit_user_with_invalid_id(): void
    {
        DB::transaction(function () {
            $this->testAsUserWithSuperAdmin();
            $id = -1;
            $response = $this->get($this->getRoute($id));
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }

    public function getRoute($id)
    {
        return route('users.edit', $id);
    }
}
