<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\AbstractMiddlewareTestCase;
use Tests\Feature\TestCaseUtils;


class ShowUserTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_a_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_can_see_a_user(): void
    {
        /** @var User $user */
        $this->loginAsNewUser();
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data', fn ($data) =>
                    !empty($data)
                )
                ->etc()
        )->assertSee(array_merge(
            [$user->name, $user->email],
            $user->permissions()->pluck('name')->toArray(),
            $user->roles()->pluck('name')->toArray()
        ));
    }
}
