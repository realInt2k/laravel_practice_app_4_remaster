<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class EditUserTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_edit_user_form(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    public function getRoute($id): string
    {
        return route('users.edit', $id);
    }

    /** @test */
    public function authenticated_without_permission_cannot_see_edit_user_form(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->loginAsNewUser();
        $response = $this->from(route('users.index'))->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey())
            ->assertRedirect(route('users.index'));
    }

    /** @test */
    public function admin_without_permission_cannot_see_edit_user_form(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $response = $this->from(route('users.index'))->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey())
            ->assertRedirect(route('users.index'));
    }

    /** @test */
    public function authenticated_with_permission_can_see_edit_user_form(): void
    {
        $user = $this->createNewUserWithRoleAndPermission('role' . Str::random(5), 'users.update');
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'users.update');
        $response = $this->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn($data) => !empty($data))
                    ->etc()
            )
            ->assertSee([$user->name, $user->email])
            ->assertDontSee(array_merge(
                $user->permissions()->pluck('name')->toArray(),
                $user->roles()->pluck('name')->toArray()
            ));
    }

    /** @test */
    public function admin_with_permission_can_see_edit_user_form(): void
    {
        $user = $this->createNewUserWithRoleAndPermission('role' . Str::random(5), 'users.update');
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.update');
        $response = $this->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn($data) => !empty($data))
                    ->etc()
            )
            ->assertSee([$user->name, $user->email])
            ->assertDontSee(array_merge(
                $user->permissions()->pluck('name')->toArray(),
                $user->roles()->pluck('name')->toArray()
            ));
    }

    /** @test */
    public function authenticated_can_see_user_profile(): void
    {
        /** @var User $user */
        $user = $this->loginAsNewUser();
        $response = $this->get(route('users.profile'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertViewIs('pages.users.user-profile')
            ->assertSee([
                'Email address', 'Name', 'Phone', 'Location',
                'Update your password', 'About', $user->name, $user->email
            ]);
    }

    /** @test */
    public function super_admin_can_see_edit_user_form(): void
    {
        $user = $this->createNewUser();
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $response = $this->get($this->getRoute($user->id));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('data', fn($data) => !empty($data))
                ->etc()
        )->assertSee([$user->name, $user->email])
            ->assertSee(array_merge(
                $user->permissions()->pluck('name')->toArray(),
                $user->roles()->pluck('name')->toArray()
            ));
    }

    /** @test */
    public function cannot_see_edit_form_with_invalid_id(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $id = -1;
        $response = $this->get($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function non_admin_user_cannot_see_edit_form_of_admin_or_super_admin_users(): void
    {
        $adminUser = $this->createNewUserWithRole($this->getAdminRole());
        $superAdminUser = $this->createNewUserWithRole($this->getSuperAdminRole());
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'users.update');
        $response = $this->get($this->getRoute($adminUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
        $response = $this->get($this->getRoute($superAdminUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_user_cannot_see_edit_form_of_super_admin_user(): void
    {
        $superAdminUser = $this->createNewUserWithRole($this->getSuperAdminRole());
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $response = $this->get($this->getRoute($superAdminUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_user_cannot_see_edit_form_of_other_admin_user(): void
    {
        $otherAdminUser = $this->createNewUserWithRole($this->getAdminRole());
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $response = $this->get($this->getRoute($otherAdminUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
    }
}
