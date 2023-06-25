<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class UpdateProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_update_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->put(route('products.update', $product->id), $product->toArray());
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function authenticated_cannot_update_product_with_invalid_id(): void
    {
        $this->testAsUser();
        $id = -1;
        $product = Product::factory()->make();
        $response = $this->put(route('products.update', $id), $product->toArray());
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function authenticated_can_update_product_with_permission(): void
    {
        $this->testAsNewUserWithRolePermission('role' . Str::random(5), 'products.update');
        $product = Product::factory()->create();
        $id = $product->id;
        $newProduct =   Product::factory()->make();
        $response = $this->from('/')->put(route('products.update', $id), $newProduct->toArray());
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data',
                    fn (AssertableJson $json) =>
                    $json
                        ->where('id', $product->id)
                        ->where('name', $newProduct->name)
                        ->where('description', $newProduct->description)
                        ->etc()
                )
                ->etc()
        );
        $this->assertDatabaseHas('products', $newProduct->toArray());
    }

    /**
     * @test
     */
    public function authenticated_cannot_update_product_with_no_products_update_permission(): void
    {
        $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'cry');
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make();
        $response = $this->put(route('products.update', $product->id), $newProduct->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /**
     * @test
     */
    public function can_update_product_with_valid_id_and_valid_data_and_products_update_permission(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'products.update');
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make($product->toArray());
        $newProduct['name'] = $product->name . ' new name';
        $newProduct['description'] = $product->name . ' new description';
        $response = $this->from(route('products.edit', $product->id))
            ->put(route('products.update', $product->id), $newProduct->toArray());
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data',
                    fn (AssertableJson $json) =>
                    $json->where('id', $newProduct->id)
                        ->etc()
                )
                ->etc()
        );
        unset($newProduct->updated_at);
        $this->assertDatabaseHas('products', $newProduct->toArray());
    }

    /**
     * @test
     */
    public function cannot_update_product_with_valid_id_and_invalid_data_and_products_update_permission(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'products.update');
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make($product->toArray());
        $newProduct['name'] = '';
        $newProduct['description'] = null;
        $response = $this->from(route('products.edit', $product->id))
            ->put(route('products.update', $product->id), $newProduct->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'description']);
        $response->assertRedirect(route('products.edit', $product->id));
    }

    /**
     * @test
     */
    public function cannot_update_product_with_valid_id_and_invalid_name_and_products_update_permission(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'products.update');
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make($product->toArray());
        $newProduct['name'] = '';
        $response = $this->from(route('products.edit', $product->id))
            ->put(route('products.update', $product->id), $newProduct->toArray());
        $response->assertSessionHasErrors(['name']);
        $response->assertStatus(302);
        $response->assertRedirect(route('products.edit', $product->id));
    }

    /**
     * @test
     */
    public function cannot_update_product_with_valid_id_and_invalid_description_and_products_update_permission(): void
    {
        $this->testAsNewUserWithRolePermission('admin', 'products.update');
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make($product->toArray());
        $newProduct['description'] = '';
        $response = $this->from(route('products.edit', $product->id))
            ->put(route('products.update', $product->id), $newProduct->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['description']);
        $response->assertStatus(302);
        $response->assertRedirect(route('products.edit', $product->id));
    }
}
