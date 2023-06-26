<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class StoreCategoryTest extends TestCaseUtils
{
    /** @test */
    public function super_admin_can_store_category(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_store_new_category_with_appropriate_permission();
    }

    /** @test */
    public function admin_can_store_category_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'categories.store');
        $this->try_to_store_new_category_with_appropriate_permission();
    }

    /** @test */
    public function non_admin_can_store_category_with_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'categories.store');
        $this->try_to_store_new_category_with_appropriate_permission();
    }

    /** @test */
    public function admin_cannot_store_category_without_appropriate_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_store_new_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function non_admin_cannot_store_category_without_appropriate_permission(): void
    {
        $this->loginAsNewUser();
        $this->try_to_store_new_category_without_appropriate_permission_then_fail();
    }

    /** @test */
    public function cannot_store_category_with_invalid_data()
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $countCategoryBefore = Category::count();
        $dataStore = $this->makeStupidData();
        $response = $this
            ->from(route('categories.create'))
            ->post(route('categories.store', $dataStore));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('categories.create'))
            ->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', $countCategoryBefore);
    }

    /** @test */
    public function cannot_store_category_if_unauthenticated()
    {
        $countCategoryBefore = Category::count();
        $dataStore = Category::factory()->make()->toArray();
        $response = $this->post(route('categories.store', $dataStore));
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('categories', $countCategoryBefore);
    }

    public function try_to_store_new_category_with_appropriate_permission(): void
    {
        $countCategoryBefore = Category::count();
        $dataStore = Category::factory()->make()->toArray();
        $response = $this->post(route('categories.store', $dataStore));
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has(
                        'data', fn(AssertableJson $json) => $json
                        ->where('name', $dataStore['name'])
                        ->etc()
                    )
                    ->etc()
            );
        $this->assertDatabaseCount('categories', $countCategoryBefore + 1)
            ->assertDatabaseHas('categories', $dataStore);
    }

    public function try_to_store_new_category_without_appropriate_permission_then_fail(): void
    {
        $countCategoryBefore = Category::count();
        $dataStore = Category::factory()->make()->toArray();
        $response = $this
            ->from(route('categories.create'))
            ->post(route('categories.store', $dataStore));
        $response
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors($this->getAuthErrorKey())
            ->assertRedirect(route('categories.create'));
        $this
            ->assertDatabaseCount('categories', $countCategoryBefore)
            ->assertDatabaseMissing('categories', $dataStore);
    }

    public function makeStupidData(): array
    {
        $data = Category::factory()->make()->toArray();
        $data['name'] = '';
        return $data;
    }
}
