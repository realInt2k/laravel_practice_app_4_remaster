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
        $response = $this->delete(route('products.destroy', $product->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_cannot_delete_a_product_without_products_destroy_permission()
    {
        $this->testAsNewUserWithRolePermission('peasant' . Str::random(10), 'crazy');
        $product = Product::factory()->create();
        $response = $this->delete(route('products.destroy', $product->id));
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    /** @test */
    public function authenticated_cannot_delete_a_product_with_products_destroy_permission_but_wrong_id()
    {
        $this->testAsNewUserWithRolePermission('user' . Str::random(10), 'products.destroy');
        $product = Product::factory()->create();
        $maxId = Product::max('id');
        $response = $this->delete(route('products.destroy', -1));
        $response->assertStatus(404);
    }

    /** @test */
    public function can_delete_a_product_with_products_destroy_permission_and_correct_id()
    {
        $this->testAsNewUserWithRolePermission('admin', 'products.destroy');
        $product = Product::factory()->create();
        $maxId = Product::max('id');
        $response = $this->delete(route('products.destroy', $maxId));
        $this->assertDatabaseMissing('products', $product->toArray());
    }
}
