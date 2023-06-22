<?php

namespace Tests\Feature\Products;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\UserRolePermissionUtility;
use Tests\Feature\AbstractMiddlewareTestCase;

class EditProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function unauthenticated_cannot_edit_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->get(route('products.edit', $product->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function authenticated_cannot_edit_product_with_invalid_id(): void
    {
        $this->testAsUser();
        $id = -1;
        $response = $this->get(route('products.edit', $id));
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function authenticated_cannot_edit_product_with_valid_id_but_without_products_update_permission(): void
    {
        $user = User::factory()->create();
        $this->assertFalse(UserRolePermissionUtility::checkIfUserHasPermission($user, 'product-update'));
        $this->actingAs($user);
        $product = Product::factory()->create();
        $response = $this->get(route('products.edit', $product->id));
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }
}
