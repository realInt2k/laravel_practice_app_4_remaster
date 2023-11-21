<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class UpdateCategoryTest extends TestCaseUtils
{
    /** @test */
    public function super_admin_can_update_category(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_update_category_with_appropriate_permission();
    }

    /** @test */
    public function admin_can_update_category_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'categories.update');
        $this->try_to_update_category_with_appropriate_permission();
    }

    /** @test */
    public function non_admin_can_update_category_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'categories.update');
        $this->try_to_update_category_with_appropriate_permission();
    }

    /** @test */
    public function admin_cannot_update_category_without_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_update_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function non_admin_cannot_update_category_without_appropriate_permission(): void
    {
        $this->loginAsNewUser();
        $this->try_to_update_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function cannot_update_with_invalid_data()
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $category = Category::factory()->create();
        $dataUpdate = $this->makeStupidData();
        $response = $this
            ->from($this->getEditViewRoute($category->id))
            ->put($this->getUpdateRoute($category->id), $dataUpdate);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect($this->getEditViewRoute($category->id))
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function can_not_update_category_if_unauthenticated()
    {
        $category = Category::factory()->create();
        $dataUpdate = Category::factory()->make()->toArray();
        $response = $this->put($this->getUpdateRoute($category->id), $dataUpdate);
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function can_not_update_if_category_doesnt_exist()
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $dataUpdate = Category::factory()->make()->toArray();
        $id = -1;
        $response = $this->put($this->getUpdateRoute($id), $dataUpdate);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function can_not_update_with_circular_parent()
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $category = Category::factory()->create();
        $dataUpdate = Category::factory()->make()->toArray();
        $dataUpdate['parent_id'] = $category->id;
        $response = $this->from(route('categories.edit', $category->id))->put($this->getUpdateRoute($category->id), $dataUpdate);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('categories.edit', $category->id))
            ->assertSessionHasErrors('parent_id');
    }

    public function try_to_update_category_with_appropriate_permission(): void
    {
        $category = Category::factory()->create();
        $dataUpdate = Category::factory()->make()->toArray();
        $response = $this->put($this->getUpdateRoute($category->id), $dataUpdate);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has(
                        'data', fn(AssertableJson $json) => $json
                        ->where('name', $dataUpdate['name'])
                        ->etc()
                    )
                    ->etc()
            );
    }

    public function try_to_update_category_without_appropriate_permission_then_fail(): void
    {
        $category = Category::factory()->create();
        $dataUpdate = Category::factory()->make()->toArray();
        $response = $this
            ->from($this->getEditViewRoute($category->id))
            ->put($this->getUpdateRoute($category->id), $dataUpdate);
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey())
            ->assertRedirect($this->getEditViewRoute($category->id));
    }

    public function makeStupidData(): array
    {
        $data = Category::factory()->make()->toArray();
        $data['name'] = '';
        return $data;
    }

    public function getEditViewRoute(int $id): string
    {
        return route('categories.edit', $id);
    }

    public function getUpdateRoute(int $id): string
    {
        return route('categories.update', $id);
    }
}
