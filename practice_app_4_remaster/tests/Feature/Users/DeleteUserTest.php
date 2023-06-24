<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class DeleteUserTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_delete_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->delete(
            $this->getRoute($user->id),
        );
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function getRoute($id): string
    {
        return route('users.destroy', $id);
    }

    /** @test */
    public function non_admin_without_permission_cannot_delete_user(): void
    {
        $this->loginAsNewUser();
        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $response = $this->delete($this->getRoute($otherUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    /** @test */
    public function admin_without_permission_cannot_delete_user(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $response = $this->delete($this->getRoute($otherUser->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas($this->getAuthErrorKey());
        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    /** @test */
    public function admin_with_permission_can_delete_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'users.destroy');
        $this->try_to_successfully_delete_user();
    }

    public function try_to_successfully_delete_user(): void
    {
        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $response = $this->from(route('users.index'))
            ->delete($this->getRoute($otherUser->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', function (AssertableJson $json) use ($otherUser) {
                    return $json
                        ->where('id', $otherUser->id)
                        ->where('email', $otherUser->email)
                        ->etc();
                })
                ->etc()
            );
        $this->assertDatabaseMissing('users', $otherUser->toArray());
    }

    /** @test */
    public function authenticated_with_permission_can_delete_user(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'users.destroy');
        $this->try_to_successfully_delete_user();
    }

    /** @test */
    public function super_admin_can_delete_user(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_successfully_delete_user();
    }

    /** @test */
    public function invalid_user_id_will_result_in_page_not_found(): void
    {
        $id = -1;
        $response = $this->from(route('users.index'))
            ->delete($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
