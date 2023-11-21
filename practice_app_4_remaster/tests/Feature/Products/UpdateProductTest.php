<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class UpdateProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_update_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->put($this->getUpdateRoute($product->id), $product->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_update_product_with_invalid_id(): void
    {
        $this->loginAsNewUser();
        $id = -1;
        $product = Product::factory()->make();
        $response = $this->put($this->getUpdateRoute($id), $product->toArray());
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function authenticated_can_update_product_with_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'products.update');
        $this->try_to_successfully_update_product();
    }

    /** @test */
    public function admin_can_update_product_with_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'products.update');
        $this->try_to_successfully_update_product();
    }

    /** @test */
    public function super_admin_can_update_product(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_successfully_update_product();
    }

    /** @test */
    public function authenticated_cannot_update_product_with_no_products_update_permission(): void
    {
        $this->loginAsNewUser();
        $this->try_to_update_product_then_fail_because_no_permission();
    }

    /** @test */
    public function admin_cannot_update_product_with_no_products_update_permission(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_update_product_then_fail_because_no_permission();
    }

    /** @test */
    public function cannot_update_product_with_empty_name(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $updateData = Product::factory()->make()->toArray();
        $updateData = ['name' => ''];
        $this->try_to_update_new_product_with_invalid_data($updateData);
    }

    /** @test */
    public function cannot_update_product_with_empty_description(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $updateData = ['description' => ''];
        $this->try_to_update_new_product_with_invalid_data($updateData);
    }

    /** @test */
    public function cannot_update_product_with_non_image_file(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $file = UploadedFile::fake()->create('failedName');
        $updateData['image'] = $file;
        $this->try_to_update_new_product_with_invalid_data($updateData);
    }

    /** @test */
    public function cannot_update_product_with_image_of_wrong_mime_type(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $file = UploadedFile::fake()->image('failed.failed');
        $updateData['image'] = $file;
        $this->try_to_update_new_product_with_invalid_data($updateData);
    }

    public function try_to_update_new_product_with_invalid_data(array $updateData): void
    {
        $product = Product::factory()->create();
        $countProductBefore = Product::count();
        $response = $this->from(route('products.edit', $product->id))
            ->put(route('products.update', $product->id), $updateData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.edit', $product->id))
            ->assertSessionHasErrors(array_keys($updateData));
        $this->assertDatabaseCount('products', $countProductBefore);
    }

    public function try_to_update_product_then_fail_because_no_permission(): void
    {
        $product = Product::factory()->create();
        $newProduct = Product::factory()->make();
        $response = $this->from(route('products.edit', $product->id))
            ->put($this->getUpdateRoute($product->id), $newProduct->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.edit', $product->id))
            ->assertSessionHasErrors($this->getAuthErrorKey());
    }

    public function try_to_successfully_update_product(): void
    {
        $product = Product::factory()->create();
        $id = $product->id;
        $newProduct = Product::factory()->make();
        $name = 'cat_' . time() . '.jpg';
        $file = UploadedFile::fake()->image($name);
        $updateData = $newProduct->toArray();
        $updateData['image'] = $file;
        $response = $this->from('/')->put(route('products.update', $id), $updateData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn(AssertableJson $json) => $json
                            ->where('id', $product->id)
                            ->where('name', $updateData['name'])
                            ->where('description', $updateData['description'])
                            ->etc()
                    )
                    ->etc()
            );
        $this->assertDatabaseHas('products', $newProduct->toArray());
    }

    public function getUpdateRoute(int $id): string
    {
        return route('products.update', $id);
    }
}
