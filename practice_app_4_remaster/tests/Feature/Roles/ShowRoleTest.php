<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\Feature\AbstractMiddlewareTestCase;

class ShowRoleTest extends AbstractMiddlewareTestCase
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

    /**
     * @test
     */
    public function cannot_see_role_with_invalid_id(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'roles.store');
        $id = -1;
        $response = $this->get(route('roles.show', -1));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
