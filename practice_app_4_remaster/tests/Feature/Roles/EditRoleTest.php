<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class EditRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_see_edit_role_form(): void
    {
        DB::transaction(function () {
            $id = random_int(0, Role::count());
            $response = $this->get(route('roles.edit', $id));
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /**
     * @test
     */
    public function admin_cannot_see_edit_role_form_with_role_update_permission(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('admin', 'roles-update');
            $role = Role::factory()->create();
            $response = $this->get(route('roles.edit', $role->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    /**
     * @test
     */
    public function authenticated_cannot_see_edit_role_form_with_invalid_id(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('python2' . Str::random(10), 'roles-update');
            $id = -1;
            $response = $this->get(route('roles.edit', $id));
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }
}
