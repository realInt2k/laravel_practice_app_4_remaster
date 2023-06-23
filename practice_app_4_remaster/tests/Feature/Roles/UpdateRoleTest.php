<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class UpdateRoleTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_update_role(): void
    {
        DB::transaction(function () {
            $id = random_int(0, Role::count());
            $response = $this->put(route('roles.update', $id));
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    }

    /**
     * @test
     */
    public function admin_without_roles_update_permission_cannot_update_role(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('admin', '');
            $role = Role::factory()->create();
            $response = $this->put(route('roles.update', $role->id));
            $response->assertStatus(302);
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
        });
    }

    /**
     * @test
     */
    public function admin_with_roles_update_permission_cannot_update_role(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('admin', 'roles.update');
            $role = Role::factory()->create();
            $newRole = Role::factory()->make($role->toArray());
            $newRole['name'] = $role->name . 'new';
            $newRole->id = $role->id;
            $response = $this->from(route('roles.edit', $role->id))
                ->put(route('roles.update', $role->id), $newRole->toArray());
            $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
            $this->assertDatabaseMissing('roles', $newRole->toArray());
        });
    }

    /**
     * @test
     */
    public function super_admin_can_update_role(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithSuperAdmin();
            $role = Role::factory()->create();
            $newRole = Role::factory()->make($role->toArray());
            $newRole['name'] = $role->name . 'new';
            $newRole->id = $role->id;
            $response = $this->from(route('roles.edit', $role->id))
                ->put(route('roles.update', $role->id), $newRole->toArray());
            $this->assertDatabaseHas('roles', $newRole->toArray());
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->where('id', $newRole->id)
                            ->etc()
                    )
                    ->etc()
            );
        });
    }

    /**
     * @test
     */
    public function cannot_update_role_with_invalid_id(): void
    {
        DB::transaction(function () {
            $this->testAsNewUserWithRolePermission('admin', 'roles.update');
            $id = -1;
            $data = ['name' => 'bullshit'];
            $response = $this->put(route('roles.update', $id), $data);
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });
    }
}
