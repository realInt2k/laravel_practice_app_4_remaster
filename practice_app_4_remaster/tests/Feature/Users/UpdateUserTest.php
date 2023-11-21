<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class UpdateUserTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_update_user(): void
    {
        $user = User::factory()->create();
        $newUserObj = User::factory()->make();
        $updateData = array_merge(
            $newUserObj->toArray(),
            ['password' => $this->makeNewPassword()]
        );
        $response = $this->put($this->getUpdateRoute($user->id), $updateData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseMissing('users', $updateData);
    }

    public function makeNewPassword(): string
    {
        return Hash::make(Str::random(10));
    }

    public function getUpdateRoute(int $id): string
    {
        return route('users.update', $id);
    }

    /** @test */
    public function non_admin_without_permission_cannot_update_user(): void
    {
        $this->loginAsNewUser();
        $this->try_to_update_then_fail();
    }

    public function try_to_update_then_fail(): void
    {
        $targetUser = User::factory()->create();
        $newUserObj = User::factory()->make();
        $updateData = array_merge(
            $newUserObj->toArray(),
            ['password' => $this->makeNewPassword()]
        );
        $response = $this->from($this->getCreateFormRoute($targetUser->id))
            ->put($this->getUpdateRoute($targetUser->id), $updateData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect($this->getCreateFormRoute($targetUser->id));
        $this->assertDatabaseMissing('users', $updateData);
    }

    public function getCreateFormRoute(int $id): string
    {
        return route('users.create', $id);
    }

    /** @test */
    public function admin_without_permission_cannot_update_user(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_update_then_fail();
    }

    /** @test */
    public function non_admin_with_permission_can_update_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'users.update');
        $this->try_to_successfully_update();
    }

    public function try_to_successfully_update(): void
    {
        $targetUser = User::factory()->create();
        $newUserObj = User::factory()->make();
        $updateData = array_merge(
            $newUserObj->toArray(),
            [
                'password' => $this->makeNewPassword()
            ]
        );
        $response = $this->from($this->getCreateFormRoute($targetUser->id))
            ->put($this->getUpdateRoute($targetUser->id), $updateData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', function (AssertableJson $json) use ($newUserObj, $targetUser) {
                    return $json
                        ->where('id', $targetUser->id)
                        ->where('email', $newUserObj->email)
                        ->where('name', $newUserObj->name)
                        ->etc();
                })
                ->etc()
            );
        $newUserObj->id = $targetUser->id;
        $this->assertDatabaseHas('users', $newUserObj->toArray());
    }

    /** @test */
    public function admin_with_permission_can_update_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.update');
        $this->try_to_successfully_update();
    }

    /** @test */
    public function super_admin_can_update_user(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_successfully_update();
    }

    /** @test */
    public function everyone_can_update_from_profile_setting(): void
    {
        $currentUser = $this->loginAsNewUser();
        $newUserObj = User::factory()->make();
        $updateData = array_merge(
            $newUserObj->toArray(),
            ['password' => $this->makeNewPassword()]
        );
        $response = $this->from(route('users.profile'))->put(route('users.profile.update'), $updateData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', function (AssertableJson $json) use ($newUserObj, $currentUser) {
                    return $json
                        ->where('id', $currentUser->id)
                        ->where('email', $newUserObj->email)
                        ->where('name', $newUserObj->name)
                        ->etc();
                })
                ->etc()
            );
        $newUserObj->id = $currentUser->id;
        $this->assertDatabaseHas('users', $newUserObj->toArray());
    }

    public function try_to_update_with_invalid_data(array $updateData): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $targetUser = User::factory()->create();
        $response = $this->from($this->getCreateFormRoute($targetUser->id))
            ->put($this->getUpdateRoute($targetUser->id), $updateData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect($this->getCreateFormRoute($targetUser->id))
            ->assertSessionHasErrors(array_keys($updateData));
        $this->assertDatabaseMissing('users', array_merge(
            ['id' => $targetUser->id],
            $updateData
        ));
    }

    /** @test */
    public function cannot_update_user_with_duplicated_email(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $otherUser = User::factory()->create();
        $this->try_to_update_with_invalid_data(['email' => $otherUser->email]);
    }

    /** @test */
    public function cannot_update_user_with_invalid_email(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_update_with_invalid_data(['email' => "lmao@gmai.dotnet"]);
    }

    /** @test */
    public function cannot_update_user_with_invalid_name(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_update_with_invalid_data(['name' => null]);
    }

    /** @test */
    public function cannot_update_user_with_name_too_long(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $str = str_repeat("abc", 100);
        $updateData = User::factory()->make()->toArray();
        $updateData['name'] = $str;
        $targetUser = User::factory()->create();
        $response = $this->from($this->getCreateFormRoute($targetUser->id))
            ->put($this->getUpdateRoute($targetUser->id), $updateData);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertDatabaseMissing('users', array_merge(
            ['id' => $targetUser->id],
            $updateData
        ));
    }
}
