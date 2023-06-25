<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class StoreCategoryTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function can_not_store_category_if_unauthenticated()
    {
        $data = $this->makeData();
        $response = $this->post($this->getRoute(), $data);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_cannot_store_category()
    {
        $data = $this->makeData();
        $this->testAsNewUser();
        $response = $this->post($this->getRoute(), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /** @test */
    public function admin_can_see_store_category_with_categories_store()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories.store');
        $data = $this->makeData();
        $response = $this->post($this->getRoute(), $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data', fn (AssertableJson $json) => $json
                    ->where('name', $data['name'])
                    ->etc()
                )
                ->etc()
        );
    }

    public function makeData()
    {
        return Category::factory()->make()->toArray();
    }

    public function getRoute()
    {
        return route('categories.store');
    }

    public function getCreateViewRoute()
    {
        return route('categories.create');
    }

    public function getTableName()
    {
        return 'categories';
    }
}
