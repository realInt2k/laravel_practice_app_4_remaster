<?php

namespace Tests\Feature\Users;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class CreateUserTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_create_user_form(): void
    {
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    public function getRoute(): string
    {
        return route('users.create');
    }

    /** @test */
    public function authenticated_without_permission_cannot_see_create_user_form(): void
    {
        $this->loginAsNewUser();
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_without_permission_cannot_see_create_user_form(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_with_permission_can_see_create_user_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.store');
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('data', fn($data) => !empty($data))
                ->etc()
        )->assertSee(['name', 'email', 'password'])
            ->assertDontSee(array_merge(
                Role::pluck('name')->toArray(),
                Permission::pluck('name')->toArray()
            ));
    }

    /** @test */
    public function authenticated_with_permission_can_see_create_user_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(10), 'users.store');
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('data', fn($data) => !empty($data))
                ->etc()
        )->assertSee(['name', 'email', 'password'])
            ->assertDontSee(array_merge(
                Role::pluck('name')->toArray(),
                Permission::pluck('name')->toArray()
            ));
    }

    /** @test */
    public function super_admin_can_see_create_user_form(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('data', fn($data) => !empty($data))
                ->etc()
        )->assertSee(array_merge(
            ['name', 'email', 'password'],
            Role::pluck('name')->toArray(),
            Permission::pluck('name')->toArray()
        ));
    }
}
