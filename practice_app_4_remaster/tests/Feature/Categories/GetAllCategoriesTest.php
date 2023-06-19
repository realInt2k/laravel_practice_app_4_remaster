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

    /** @test */
    public function user_can_not_get_list_category_if_has_not_permission()
    {
        $this->testAsNewUser();
        $response = $this->get($this->getRoute());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
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
