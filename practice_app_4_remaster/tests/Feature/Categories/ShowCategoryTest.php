<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class ShowCategoryTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function admin_can_get_category()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-what');
        $data = $this->createData();
        $response = $this->get($this->getRoute($data->id));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data'
                )
                ->etc()
        );
        $response->assertSee($data->name);
    }

    /** @test */
    public function can_not_get_category_if_unauthenticated()
    {
        $data = $this->createData();
        $response = $this->get($this->getRoute($data->id));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_not_get_if_category_not_exist()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories.store');
        $response = $this->get($this->getRoute(-1));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function getRoute($id)
    {
        return route('categories.show', $id);
    }

    public function getView()
    {
        return 'categories.show';
    }

    public function createData()
    {
        return Category::factory()->create();
    }
}