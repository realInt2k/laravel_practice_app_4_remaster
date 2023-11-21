<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class EditCategoryTest extends TestCaseUtils
{
    /** @test */
    public function can_not_see_edit_category_form_if_unauthenticated(): void
    {
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_cannot_see_edit_category_form_without_appropriate_permission(): void
    {
        $this->loginAsNewUser();
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_cannot_see_edit_category_form_without_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_can_see_edit_category_form_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'categories.update');
        $this->try_to_get_category_create_form_with_appropriate_permission();
    }

    /** @test */
    public function non_admin_can_see_edit_category_form_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'categories.update');
        $this->try_to_get_category_create_form_with_appropriate_permission();
    }

    public function try_to_get_category_create_form_with_appropriate_permission(): void
    {
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_OK);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn ($data) =>
                        !empty($data)
                    )
                    ->etc()
            );
    }

    public function getRoute(int $id): string
    {
        return route('categories.edit', $id);
    }
}
