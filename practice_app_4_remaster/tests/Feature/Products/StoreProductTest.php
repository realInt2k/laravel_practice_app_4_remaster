<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class StoreProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_store_product(): void
    {
        $data = Product::factory()->make();
        $numberOfProducts = Product::count();
        $response = $this->post(route('products.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', $numberOfProducts);
    }

    /**
     * @test
     */
    public function authenticated_cannot_store_product_without_products_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('criminal' . Str::random(10), 'products-steal');
        $data = Product::factory()->make();
        $response = $this->from(route('products.create'))
            ->post(route('products.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /**
     * @test
     */
    public function authenticated_can_store_product_with_products_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'products-store');
        $numberOfProductPriorToStoring = Product::count();
        $data = Product::factory()->make();
        $response = $this->from(route('products.create'))
            ->post(route('products.store'), $data->toArray());
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data',
                    fn (AssertableJson $json) =>
                    $json
                        ->where('name', $data->name)
                        ->where('description', $data->description)
                        ->etc()
                )
                ->etc()
        );
        $numberOfProductAfterStoring = Product::count();
        $this->assertEquals($numberOfProductAfterStoring, $numberOfProductPriorToStoring + 1);
    }

    /**
     * @test
     */
    public function authenticated_cannot_store_product_with_invalid_name_even_with_products_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'products-store');
        $oldNumberOfProducts = Product::count();
        $data = Product::factory()->make();
        $data['name'] = '';
        $response = $this->post(route('products.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('products', $oldNumberOfProducts);
    }

    /**
     * @test
     */
    public function authenticated_cannot_store_product_with_invalid_description_even_with_products_store_permission(): void
    {
        $user = $this->testAsNewUserWithRolePermission('admin', 'products-store');
        $oldNumberOfProducts = Product::count();
        $data = Product::factory()->make();
        $data['description'] = '';
        $response = $this->post(route('products.store'), $data->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['description']);
        $this->assertDatabaseCount('products', $oldNumberOfProducts);
    }
}
