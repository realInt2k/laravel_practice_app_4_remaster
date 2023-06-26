<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class CreateProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_create_see_create_product_form(): void
    {
        $countProductBefore = Product::count();
        $response = $this->get(route('products.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', $countProductBefore);
    }

    /** @test */
    public function authenticated_without_permission_cannot_see_create_product_form(): void
    {
        $this->loginAsNewUser();
        $this->cannot_see_product_create_form_without_permission();
    }

    public function cannot_see_product_create_form_without_permission(): void
    {
        $response = $this->from(route('users.profile'))->get(route('products.create'));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('users.profile'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    /** @test */
    public function admin_without_permission_cannot_see_create_product_form(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->cannot_see_product_create_form_without_permission();
    }

    /** @test */
    public function authenticated_with_permission_can_see_create_product_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission(
            'role' . Str::random(5),
            'products.store'
        );
        $this->can_see_product_create_form_with_correct_permission();
    }

    public function can_see_product_create_form_with_correct_permission(): void
    {
        $response = $this->get(route('products.create'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn($data) => !empty($data)
                    )
                    ->etc()
            )->assertSee(['name', 'description', 'Category assignment', 'Upload an image']);
    }

    /** @test */
    public function admin_with_permission_can_see_create_product_form(): void
    {
        $this->loginAsNewUserWithRoleAndPermission(
            $this->getAdminRole(),
            'products.store'
        );
        $this->can_see_product_create_form_with_correct_permission();
    }

    /** @test */
    public function super_admin_can_see_create_product_form(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->can_see_product_create_form_with_correct_permission();
    }
}
