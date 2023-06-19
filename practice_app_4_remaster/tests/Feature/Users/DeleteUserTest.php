<?php

namespace Tests\Feature\Users;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\Feature\AbstractMiddlewareTestCase;

class DeleteUserTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function unauthenticated_cannot_delete_user(): void
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $response = $this->delete(
                route('users.destroy', $user->id),
                $this->getRequestData(3, 3, 3, route('users.index'), 1)
            );
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
            $this->assertDatabaseHas('users', $user->toArray());
        });
    }

    /** @test */
    public function authenticated_cannot_delete_user_with_no_super_admin_and_no_user_destroy_permission_and_isnt_that_user(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(3, 3, 3, route('users.index'), 1);
            $currentUser = $this->testAsNewUser();
            $otherUser = User::factory()->create();
            $response = $this->delete(route('users.destroy', $otherUser->id), $requestData);
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.authenticationErrorKey'));
        });
    }

    /** @test */ // deprecated (no support for this)
    // public function authenticated_can_delete_user_with_no_super_admin_and_no_user_destroy_permission_but_is_that_user(): void
    // {
    //     DB::beginTransaction();
    //     try {
    //         $requestData = $this->getRequestData(3, 3, 3, route('users.index'), 1);
    //         $currentUser = $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'users-destroy');
    //         $response = $this->from(route('users.index'))
    //             ->delete(route('users.destroy', $currentUser->id), $requestData);
    //         $response->assertSessionMissing(config('constants.authenticationErrorKey'));
    //         $response->assertStatus(302);
    //         $response->assertRedirect();
    //         $this->assertDatabaseMissing('users', $currentUser->toArray());
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }

    /** @test */
    public function admin_can_delete_user_with_user_destroy_permission(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(3, 3, 3, route('users.index'), 1);
            $currentUser = $this->testAsNewUserWithRolePermission('admin', 'users-destroy');
            $otherUser = User::factory()->create();
            $response = $this->from(route('users.index'))
                ->delete(route('users.destroy', $otherUser->id), $requestData);
            $response->assertSessionMissing(config('constants.authenticationErrorKey'));
            $response->assertStatus(Response::HTTP_NO_CONTENT);
            $this->assertDatabaseMissing('users', $otherUser->toArray());
        });
    }

    /** @test */
    public function authenticated_as_super_admin_can_delete_user(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(3, 3, 3, route('users.index'), 1);
            $currentUser = $this->testAsNewUserWithSuperAdmin();
            $otherUser = User::factory()->create();
            $response = $this->from(route('users.index'))
                ->delete(route('users.destroy', $otherUser->id), $requestData);
            $response->assertSessionMissing(config('constants.authenticationErrorKey'));
            $response->assertStatus(Response::HTTP_NO_CONTENT);
            $this->assertDatabaseMissing('users', $otherUser->toArray());
        });
    }

    /** @test */
    public function invalid_user_id_will_result_in_page_not_found(): void
    {
        DB::transaction(function () {
            $requestData = $this->getRequestData(3, 3, 3, route('users.index'), 1);
            $currentUser = $this->testAsNewUserWithRolePermission('death' . Str::random(10), 'users-destroy');
            $response = $this->from(route('users.index'))
                ->delete(route('users.destroy', -1), $requestData);
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }
}
