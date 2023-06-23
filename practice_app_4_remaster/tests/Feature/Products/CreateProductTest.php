<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class CreateProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_create_product(): void
    {
        $data = Product::factory()->make();
        $numberOfProducts = Product::count();
        $response = $this->get(route('products.create'), $data->toArray());
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', $numberOfProducts);
    }

    /**
     * @test
     */
    public function authenticated_can_see_create_product_with_products_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'products.store');
        $data = Product::factory()->make();
        $response = $this->get(route('products.create'), $data->toArray());
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data'
                )
                ->etc()
        );
    }

    /**
     * @test
     */
    public function authenticated_cannot_create_product_without_products_create_permission(): void
    {
        $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'cry');
        $data = Product::factory()->make();
        $response = $this->get(route('products.create'), $data->toArray());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }
}
