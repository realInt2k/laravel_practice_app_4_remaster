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
    public function cannot_see_edit_form_without_permission(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $this->testAsNewUser();
            $response = $this->get($this->getRoute($user->id));
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
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
    public function can_see_edit_form_as_super_admin(): void
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
                        'data'
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
    public function can_see_edit_form_with_permission(): void
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
                        'data'
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
    public function cannot_see_edit_form_with_invalid_id(): void
    {
        DB::transaction(function () {
            $this->testAsUserWithSuperAdmin();
            $id = -1;
            $response = $this->get($this->getRoute($id));
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }

    /** @test */
    public function normal_user_cannot_see_edit_form_of_admin_users(): void
    {
        DB::transaction(function () {
            $adminUser = $this->testAsNewUserWithRolePermission('admin', 'users-update');
            $user = $this->testAsNewUser();
            $response = $this->get($this->getRoute($adminUser->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    /** @test */
    public function admin_user_cannot_see_edit_form_of_super_admin_user(): void
    {
        DB::transaction(function () {
            $superAdminUser = $this->testAsNewUserWithSuperAdmin();
            $user = $this->testAsNewUserWithRolePermission('admin', 'users-update');
            $response = $this->get($this->getRoute($superAdminUser->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    public function getRoute($id)
    {
        return route('users.edit', $id);
    }
}
