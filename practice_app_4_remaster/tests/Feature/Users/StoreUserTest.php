<?php

namespace Tests\Feature\Users;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class StoreUserTest extends TestCaseUtils
{
    const NO_PERMISSIONS = 5;
    const NO_ROLES = 3;
    /** @test */
    public function unauthenticated_can_not_store_new_user(): void
    {
        $countUserBefore = User::count();
        $storeData = $storeData = $this->makeNewStoreUserData();
        $response = $this->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('users', $countUserBefore);
    }

    public function makeNewPassword(): string
    {
        return Hash::make(Str::random(10));
    }

    public function makeNewStoreUserData(): array
    {
        return array_merge(
            User::factory()->make()->toArray(),
            ['password' => $this->makeNewPassword()]
        );
    }

    public function try_to_store_user_without_permission_then_fail(): void
    {
        $countUserBefore = User::count();
        $storeData = $this->makeNewStoreUserData();
        $response = $this->from(route('users.index'))
            ->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
        $this->assertDatabaseCount('users', $countUserBefore);
    }

    public function try_to_successfully_store_new_user(array $storeData): void
    {
        $countUserBefore = User::count();
        $response = $this->from(route('users.index'))
            ->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', fn (AssertableJson $json) => $json
                    ->where('name', $storeData['name'])
                    ->where('email', $storeData['email'])
                    ->etc()
                )
                ->etc()
            );
        $this->assertDatabaseCount('users', $countUserBefore + 1)
            ->assertDatabaseHas('users', $storeData);
    }

    /** @test */
    public function authenticated_without_permission_cannot_store_new_user(): void
    {
        $this->loginAsNewUser();
        $this->try_to_store_user_without_permission_then_fail();
    }

    /** @test */
    public function admin_without_permission_cannot_store_new_user(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_store_user_without_permission_then_fail();
    }

    /** @test */
    public function authenticated_with_permission_can_store_new_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'users.store');
        $storeData = $this->makeNewStoreUserData();
        $this->try_to_successfully_store_new_user($storeData);
    }

    /** @test */
    public function admin_with_permission_can_store_new_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.store');
        $storeData = $this->makeNewStoreUserData();
        $this->try_to_successfully_store_new_user($storeData);
    }

    /** @test */
    public function super_admin_can_store_new_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.store');
        $permissions = Permission::factory(self::NO_PERMISSIONS)->create();
        $roles = Role::factory(self::NO_ROLES)->create();
        $storeData = array_merge(
            User::factory()->make()->toArray(),
            [
                'password' => $this->makeNewPassword(),
                'permissions' => $permissions->pluck('id')->toArray(),
                'roles' => $roles->pluck('id')->toArray()
            ]
        );
        $countUserBefore = User::count();
        $response = $this->from(route('users.index'))
            ->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', fn (AssertableJson $json) => $json
                    ->where('name', $storeData['name'])
                    ->where('email', $storeData['email'])
                    ->etc()
                )
                ->etc()
            );
        unset($storeData['permissions']);
        unset($storeData['roles']);
        $this->assertDatabaseCount('users', $countUserBefore + 1)
            ->assertDatabaseHas('users', $storeData);
        $newUser = User::where('email', $storeData['email'])->first();
        foreach($roles as $role) {
            $this->assertDatabaseHas('user_role', [
                'role_id' => $role->id,
                'user_id' => $newUser->id
            ]);
        }
        foreach($permissions as $permission) {
            $this->assertDatabaseHas('user_permission', [
                'permission_id' => $permission->id,
                'user_id' => $newUser->id
            ]);
        }
    }

    public function try_to_store_with_invalid_data(array $storeData): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $countUserBefore = User::count();
        $response = $this->from(route('users.index'))
            ->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(array_keys($storeData));
        $this->assertDatabaseCount('users', $countUserBefore);
    }

    /** @test */
    public function cannot_store_new_user_with_duplicate_email(): void
    {
        $otherUser = User::factory()->create();
        $storeData = [
            'email' => $otherUser->email,
        ];
        $this->try_to_store_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_new_user_with_empty_email(): void
    {
        $storeData = [
            'email' => '',
        ];
        $this->try_to_store_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_new_user_with_empty_password(): void
    {
        $storeData = [
            'password' => '',
        ];
        $this->try_to_store_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_new_user_with_empty_name(): void
    {
        $storeData = [
            'name' => '',
        ];
        $this->try_to_store_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_user_with_name_too_long(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $countUserBefore = User::count();
        $str = str_repeat("abc", 1000);
        $storeData = User::factory()->make()->toArray();
        $storeData['name'] = $str;
        $storeData['password'] = $this->makeNewPassword();
        $response = $this->from(route('users.create'))
            ->post(route('users.store'), $storeData);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertDatabaseCount('users', $countUserBefore);
    }
}
