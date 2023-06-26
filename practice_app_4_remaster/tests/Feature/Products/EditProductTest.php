<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class EditProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_edit_product_form(): void
    {
        $product = Product::factory()->create();
        $response = $this->get($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    public function getRoute(int $id): string
    {
        return route('products.edit', $id);
    }

    /** @test */
    public function cannot_see_edit_product_form_with_invalid_id(): void
    {
        $this->loginAsNewUser();
        $id = -1;
        $response = $this->get($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function authenticated_without_permission_cannot_see_edit_product_form(): void
    {
        $this->loginAsNewUser();
        $this->cannot_see_product_edit_form_without_permission();
    }

    /** @test */
    public function admin_without_permission_cannot_see_edit_product_form(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->cannot_see_product_edit_form_without_permission();
    }

    public function cannot_see_product_edit_form_without_permission(): void
    {
        $product = Product::factory()->create();
        $response = $this->from(route('products.index'))->get($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function authenticated_with_permission_can_see_edit_product_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission(
            'role' . Str::random(5),
            'products.update'
        );
        $this->can_see_product_edit_form_with_correct_permission();
    }

    /** @test */
    public function admin_with_permission_can_see_edit_product_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission(
            $this->getAdminRole(),
            'products.update'
        );
        $this->can_see_product_edit_form_with_correct_permission();
    }

    /** @test */
    public function super_admin_can_see_edit_product_form(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->can_see_product_edit_form_with_correct_permission();
    }

    public function can_see_product_edit_form_with_correct_permission(): void
    {
        $product = Product::factory()->withRandomPhoto()->create();
        $response = $this->from(route('products.index'))->get($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data', fn($data) => !empty($data)
                    && str_contains($data, $product->imagePath)
                )
                ->etc()
            )
            ->assertSee([
                $product->name, $product->description
            ]);
    }
}
