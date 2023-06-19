<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Tests\Feature\AbstractMiddlewareTestCase;

class DestroyProductTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function unauthenticated_cannot_delete_a_product()
    {
        $product = Product::factory()->create();
        $requestData = $this->getRequestData(1, 3, 3, route('products.index'), 3);
        $response = $this->delete(route('products.destroy', $product->id), $requestData);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_cannot_delete_a_product_without_products_destroy_permission()
    {
        $requestData = $this->getRequestData(1, 3, 3, route('products.index'), 3);
        $this->testAsNewUserWithRolePermission('peasant' . Str::random(10), 'crazy');
        $product = Product::factory()->create();
        $response = $this->delete(route('products.destroy', $product->id), $requestData);
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    /** @test */
    public function authenticated_cannot_delete_a_product_with_products_destroy_permission_but_wrong_id()
    {
        $requestData = $this->getRequestData(1, 3, 3, route('products.index'), 3);
        $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'products-destroy');
        $product = Product::factory()->create();
        $maxId = Product::max('id');
        $response = $this->delete(route('products.destroy', -1), $requestData);
        $response->assertStatus(404);
    }

    /** @test */
    public function can_delete_a_product_with_products_destroy_permission_and_correct_id()
    {
        $requestData = $this->getRequestData(1, 3, 3, route('products.index'), 3);
        $this->testAsNewUserWithRolePermission('admin', 'products-destroy');
        $product = Product::factory()->create();
        $maxId = Product::max('id');
        $response = $this->delete(route('products.destroy', $maxId), $requestData);
        $this->assertDatabaseMissing('products', $product->toArray());
    }
}
