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
                $this->getRoute($user->id),
            );
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
            $this->assertDatabaseHas('users', $user->toArray());
        });
    }

    /** @test */
    public function non_admin_cannot_delete(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUser();
            $otherUser = User::factory()->create();
            $response = $this->delete($this->getRoute($otherUser->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    /** @test */
    public function admin_can_not_delete_user_without_user_destroy_permission(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUserWithRolePermission('admin', Str::random(5));
            $otherUser = User::factory()->create();
            $response = $this->from(route('users.index'))
                ->delete($this->getRoute($otherUser->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    /** @test */
    public function admin_can_delete_user_with_user_destroy_permission(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUserWithRolePermission('admin', 'users-destroy');
            $otherUser = User::factory()->create();
            $response = $this->from(route('users.index'))
                ->delete($this->getRoute($otherUser->id));
            $response->assertSessionMissing(config('constants.AUTHENTICATION_ERROR_KEY'));
            $response->assertStatus(Response::HTTP_NO_CONTENT);
            $this->assertDatabaseMissing('users', $otherUser->toArray());
        });
    }

    /** @test */
    public function authenticated_as_super_admin_can_delete_user(): void
    {
        DB::transaction(function () {
            $currentUser = $this->testAsNewUserWithSuperAdmin();
            $otherUser = User::factory()->create();
            $response = $this->from(route('users.index'))
                ->delete($this->getRoute($otherUser->id));
            $response->assertSessionMissing(config('constants.AUTHENTICATION_ERROR_KEY'));
            $response->assertStatus(Response::HTTP_NO_CONTENT);
            $this->assertDatabaseMissing('users', $otherUser->toArray());
        });
    }

    /** @test */
    public function invalid_user_id_will_result_in_page_not_found(): void
    {
        DB::transaction(function () {
            $id = -1;
            $response = $this->from(route('users.index'))
                ->delete($this->getRoute($id));
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }

    public function getRoute($id)
    {
        return route('users.destroy', $id);
    }
}
