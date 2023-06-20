<?php

namespace Tests\Feature\categories;

use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class CreateCategoryTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function can_not_create_category_if_unauthenticated()
    {
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_cannot_create_category()
    {
        $this->testAsNewUser();
        $response = $this->get($this->getRoute());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /** @test */
    public function admin_can_see_create_category_form_with_categories_store()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-store');
        $response = $this->get($this->getRoute());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data',
                )
                ->etc()
        );
    }

    public function getRoute()
    {
        return route('categories.create');
    }

    public function getIndexRoute()
    {
        return route('categories.index');
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
