<?php

namespace Tests\Feature\Products;

use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class GetAllProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_product_page(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }
    /** @test */
    public function user_can_get_all_products(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('products.index'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertViewIs('pages.products.index')
            ->assertViewHas('categories');
    }

    /** @test */
    public function user_can_see_product_table(): void
    {
        $this->loginAsNewUser();
        $response = $this->get(route('products.search'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', fn ($data) => !empty($data))
                ->etc()
            )
            ->assertSee(['ID', 'NAME', 'CATEGORY', 'DESCRIPTION', 'VISUAL']);
    }
}
