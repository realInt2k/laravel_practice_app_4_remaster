<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class CreateRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_create_role_form(): void
    {
        $response = $this->get(route('roles.create'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_without_roles_create_permission_and_super_admin_cannot_see_create_role_form(): void
    {
        $this->testAsNewUser();
        $response = $this->get(route('roles.create'));
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /** @test */
    public function admin_with_roles_store_permission_can_see_create_role_form(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'roles-store');
        $response = $this->get(route('roles.create'));
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'html',
                )
                ->etc()
        );
        $response->assertSee('name');
        $allPermissionNames = Permission::pluck('name')->toArray();
    }
}
