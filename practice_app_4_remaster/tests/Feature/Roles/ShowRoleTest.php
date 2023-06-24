<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class ShowRoleTest extends TestCaseUtils
{
    /** @test */
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
            ->assertSessionHas($this->getAuthErrorKey());
    }

    /** @test */
    public function super_admin_can_see_role_and_its_permissions(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $role = Role::factory()->withRandomPermissions(5)->create();
        $response = $this
            ->from(route('users.profile'))
            ->get(route('roles.show', $role->id));
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data',
                    fn($data) => !empty($data)
                        && str_contains($data, $role->name)
                )
                ->etc()
            )
            ->assertSee($role->permissions()->pluck('name')->toArray());
    }

    /** @test */
    public function cannot_see_role_with_invalid_id(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $id = -1;
        $response = $this->get(route('roles.show', $id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
