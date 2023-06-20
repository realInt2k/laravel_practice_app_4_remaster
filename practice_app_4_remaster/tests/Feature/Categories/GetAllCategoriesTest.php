<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Http\Response;
use Tests\Feature\AbstractMiddlewareTestCase;

class GetAllCategoriesTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function admin_can_get_list_category()
    {
        $this->withoutExceptionHandling();
        $this->testAsNewUserWithRolePermission('admin', 'categories-store');
        $category = $this->createCategory();
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs($this->getView());
    }

    /** @test */
    public function can_not_get_list_category_if_unauthenticated()
    {
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    public function createCategory()
    {
        return Category::factory()->create();
    }

    public function getRoute()
    {
        return route('categories.index');
    }

    public function getView()
    {
        return 'pages.categories.index';
    }
}
