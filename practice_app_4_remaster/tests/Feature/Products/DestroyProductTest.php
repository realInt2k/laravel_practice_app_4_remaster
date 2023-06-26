<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class DestroyProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_delete_a_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_cannot_delete_a_product_without_products_destroy_permission(): void
    {
        $this->loginAsNewUser();
        $product = Product::factory()->create();
        $response = $this->from(route('products.index'))
            ->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_cannot_delete_a_product_without_products_destroy_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $product = Product::factory()->create();
        $response = $this->from(route('products.index'))
            ->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function cannot_delete_product_when_provide_invalid_id()
    {
        $productCountBefore = Product::count();
        $this->loginAsNewUser();
        $id = -1;
        $response = $this->delete($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertDatabaseCount('products', $productCountBefore);
    }

    /** @test */
    public function non_admin_can_delete_a_product_with_products_destroy_permission_and_correct_id(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'products.destroy');
        $product = Product::factory()->create();
        $response = $this->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function admin_can_delete_a_product_with_products_destroy_permission_and_correct_id(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'products.destroy');
        $product = Product::factory()->create();
        $response = $this->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function super_admin_can_delete_a_product_with_products_destroy_permission_and_correct_id(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $product = Product::factory()->create();
        $response = $this->delete($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function getRoute(int $id): string
    {
        return route('products.destroy', $id);
    }
}
