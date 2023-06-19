<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class UpdateUserTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_update_user(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $newData = User::factory()->make();
            $updateArray = array_merge(
                $newData->toArray(),
                ['password' => 'brand new password']
            );
            $response = $this->put(route('users.update', $user->id), $updateArray);
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /** @test */
    public function authenticated_cannot_update_with_no_super_admin_and_no_users_update_permission_or_isnt_that_user(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUser();
            $otherUser = User::factory()->create();
            $newData = User::factory()->make();
            $updateArray = array_merge(
                $newData->toArray(),
                ['password' => 'brand new password']
            );
            $response = $this->put(route('users.update', $otherUser->id), $updateArray);
            $response->assertStatus(302);
            $response->assertSessionHas(config("constants.authenticationErrorKey"));
        });
    }

    /** @test */
    public function admin_can_update_user_with_user_update_permission(): void
    {
        DB::transaction(function () {
            /** @var User */
            $currentUser = $this->testAsNewUserWithRolePermission('admin', 'users-update');
            $otherUser = User::factory()->create();
            $newData = User::factory()->make();
            $updateArray = array_merge(
                $newData->toArray(),
                [
                    'password' => 'brand new password'
                ]
            );
            $response = $this->from(route('users.index'))->put(route('users.update', $otherUser->id), $updateArray);
            $response->assertSessionMissing(config('constants.authenticationErrorKey'));
            $response->assertStatus(200);
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->where('id', $otherUser->id)
                            ->etc()
                    )
                    ->etc()
            );
            $newData->id = $otherUser->id;
            $this->assertEquals($newData->email_verified_at, $otherUser->email_verified_at);
            unset($newData['email_verified_at']);
            $this->assertDatabaseHas('users', $newData->toArray());
        });
    }

    /** @test */
    public function authenticated_can_update_with_no_super_admin_and_no_user_update_permission_but_is_that_user(): void
    {
        $this->withoutExceptionHandling();
        DB::transaction(function () {
            /** @var User */
            $currentUser = $this->testAsNewUser();
            $newData = User::factory()->make();
            $updateArray = array_merge(
                $newData->toArray(),
                [
                    'password' => 'brand new password'
                ]
            );
            $response = $this->from(route('user-profile'))->put(route('user-profile.update'), $updateArray);
            $response->assertSessionMissing(config('constants.authenticationErrorKey'));
            $response->assertStatus(200);
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->where('id', $currentUser->id)
                            ->etc()
                    )
                    ->etc()
            );
            $newData->id = $currentUser->id;
            $this->assertDatabaseHas('users', $newData->toArray());
        });
    }

    /** @test */
    public function super_admin_can_update_user(): void
    {
        DB::transaction(function () {
            /** @var User */
            $currentUser = $this->testAsNewUserWithSuperAdmin();
            $otherUser = User::factory()->create();
            $newRole = Role::factory()->create();
            $otherUserRoles = [$newRole->id];
            $newData = User::factory()->make();
            $updateArray = array_merge(
                $newData->toArray(),
                [
                    'roles' => $otherUserRoles,
                    'password' => 'brand new password'
                ]
            );
            $response = $this->from(route('users.index'))->put(route('users.update', $otherUser->id), $updateArray);
            $response->assertSessionMissing(config('constants.authenticationErrorKey'));
            $response->assertStatus(200);
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->where('id', $otherUser->id)
                            ->etc()
                    )
                    ->etc()
            );
            $newData->id = $otherUser->id;
            $this->assertDatabaseHas('users', $newData->toArray());
            $this->assertEquals($otherUser->roles()->pluck('id')->toArray(), [$newRole->id]);
        });
    }

    /** @test */
    public function cannot_update_with_duplicated_email(): void
    {
        DB::transaction(function () {
            /** @var User */
            $currentUser = $this->testAsNewUserWithSuperAdmin();
            $otherUser = User::factory()->create();
            $updateArray = $this->getUpdateData();
            $updateArray['email'] = $currentUser->email;
            $response = $this->from(route('users.edit', $otherUser->id))->put(route('users.update', $otherUser->id), $updateArray);
            $response->assertStatus(302);
            $response->assertSessionHasErrors(['email']);
            $response->assertRedirect(route('users.edit', $otherUser->id));
            unset($currentUser['email_verified_at']);
            unset($currentUser['updated_at']);
            unset($currentUser['created_at']);
            $this->assertDatabaseHas('users', $currentUser->toArray());
        });
    }

    public function getUpdateData()
    {
        $newData = User::factory()->make();
        $updateArray = array_merge(
            $newData->toArray(),
            ['password' => Str::random(10)]
        );
        return $updateArray;
    }
}
