<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;
use Illuminate\Support\Facades\DB;

class ShowRoleTest extends TestCaseUtils
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_role(): void
    {
        DB::transaction(function () {
            $role = Role::factory()->create();
            $response = $this->get(route('roles.show', $role->id));
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /** @test */
    public function non_admin_cannot_see_role(): void
    {
        $this->loginAsNewUser();
        $role = Role::factory()->create();
        $response = $this
            ->from(route('users.profile'))
            ->get(route('roles.show', $role->id));
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHas(config('custom.aliases.auth_error_key'));
    }

    /**
     * @test
     */
    public function cannot_see_role_with_invalid_id(): void
    {
        $this->loginAsNewUserWithRole('super-admin');
        $id = -1;
        $response = $this->get(route('roles.show', $id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
