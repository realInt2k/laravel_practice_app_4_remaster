<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class DestroyCategoryTest extends TestCaseUtils
{
    /** @test */
    public function user_cannot_delete_category_without_appropriate_permission()
    {
        $this->loginAsNewUser();
        $this->try_to_delete_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function user_can_delete_category_with_appropriate_permission()
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_delete_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function admin_can_delete_category_with_appropriate_permission()
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole() . Str::random(5), 'categories.destroy');
        $this->try_to_delete_category_with_appropriate_permission();
    }

    /** @test */
    public function super_admin_can_delete_categor()
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole() . Str::random(5), 'categories.destroy');
        $this->try_to_delete_category_with_appropriate_permission();
    }

    /** @test */
    public function can_not_delete_category_if_unauthenticated()
    {
        $category = Category::factory()->create();
        $response = $this->delete($this->getRoute($category->id));
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function can_not_delete_category_if_is_not_exist()
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $id = -1;
        $response = $this->delete($this->getRoute($id));
        $response
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function getRoute($id)
    {
        return route('categories.destroy', $id);
    }

    public function try_to_delete_category_with_appropriate_permission(): void
    {
        $category = Category::factory()->create();
        $countCategoryBefore = Category::count();
        $response = $this->delete($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount('categories', $countCategoryBefore - 1)
            ->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function try_to_delete_category_without_appropriate_permission_then_fail(): void
    {
        $category = Category::factory()->create();
        $countCategoryBefore = Category::count();
        $response = $this
            ->from(route('categories.index'))
            ->delete($this->getRoute($category->id));
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('categories.index'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
        $this
            ->assertDatabaseCount('categories', $countCategoryBefore)
            ->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
