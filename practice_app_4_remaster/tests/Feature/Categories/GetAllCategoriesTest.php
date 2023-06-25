<?php

namespace Tests\Feature\categories;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class GetAllCategoriesTest extends TestCaseUtils
{
    /** @test */
    public function everyone_can_see_category_index()
    {
        $this->loginAsNewUser();
        $response = $this->get(route('categories.index'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertViewIs('pages.categories.index');
    }

    /** @test */
    public function everyone_can_get_list_category()
    {
        $this->loginAsNewUser();
        $response = $this->get(route('categories.search'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', fn ($data)
                    => !empty($data)
                )
                ->etc()
            )
            ->assertSee(['ID', 'NAME', 'PARENT CATEGORY', 'CHILDREN CATEGORIES']);
    }

    /** @test */
    public function can_not_get_list_category_if_unauthenticated()
    {
        $response = $this->get(route('categories.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }
}
