<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class CreateCategoryTest extends TestCaseUtils
{
    /** @test */
    public function can_not_see_create_category_form_if_unauthenticated(): void
    {
        $response = $this->get(route('categories.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_cannot_see_create_category_form_without_appropriate_permission(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('categories.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_cannot_see_create_category_form_without_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $response = $this->get(route('categories.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_can_see_create_category_form_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'categories.store');
        $this->try_to_get_category_create_form_with_appropriate_permission();
    }

    /** @test */
    public function non_admin_can_see_create_category_form_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'categories.store');
        $this->try_to_get_category_create_form_with_appropriate_permission();
    }

    public function try_to_get_category_create_form_with_appropriate_permission(): void
    {
        $response = $this->get(route('categories.create'));
        $response->assertStatus(Response::HTTP_OK);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn ($data) => !empty($data))
                    ->etc()
            );
    }
}
