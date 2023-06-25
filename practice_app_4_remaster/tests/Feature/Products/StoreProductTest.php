<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Faker\Factory as FakerFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class StoreProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_store_product(): void
    {
        $data = Product::factory()->make();
        $numberOfProducts = Product::count();
        $response = $this->post(route('products.store'), $data->toArray());
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', $numberOfProducts);
    }

    /** @test */
    public function authenticated_without_permission_cannot_store_product(): void
    {
        $this->loginAsNewUser();
        $this->try_to_store_new_product_without_permission();
    }

    /** @test */
    public function admin_without_permission_cannot_store_product(): void
    {
        $this->loginAsNewUserWithRole($this->getAdminRole());
        $this->try_to_store_new_product_without_permission();
    }

    /** @test */
    public function authenticated_can_store_product_with_products_store_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission('role' . Str::random(5), 'products.store');
        $this->try_to_store_new_product_with_permission();
    }

    /** @test */
    public function admin_can_store_product_with_products_store_permission(): void
    {
        $this->loginAsNewUserWithRoleAndPermission($this->getAdminRole(), 'products.store');
        $this->try_to_store_new_product_with_permission();
    }

    /** @test */
    public function super_admin_can_store_product(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $this->try_to_store_new_product_with_permission();
    }

    /** @test */
    public function cannot_store_product_with_empty_name(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $storeData = ['name' => ''];
        $this->try_to_store_new_product_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_product_with_empty_description(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $storeData = ['description' => ''];
        $this->try_to_store_new_product_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_product_with_non_image_file(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $file = UploadedFile::fake()->create('failedName');
        $storeData['image'] = $file;
        $this->try_to_store_new_product_with_invalid_data($storeData);
    }

    /** @test */
    public function cannot_store_product_with_image_of_wrong_mime_type(): void
    {
        $this->loginAsNewUserWithRole($this->getSuperAdminRole());
        $file = UploadedFile::fake()->image('failed.failed');
        $storeData['image'] = $file;
        $this->try_to_store_new_product_with_invalid_data($storeData);
    }

    public function try_to_store_new_product_without_permission(): void
    {
        $storeData = $this->makeStoreProductData();
        $countProductBefore = Product::count();
        $response = $this->from(route('products.create'))
            ->post(route('products.store'), $storeData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors($this->getAuthErrorKey());
        $this->assertDatabaseCount('products', $countProductBefore);
    }

    public function try_to_store_new_product_with_invalid_data(array $storeData): void
    {
        $countProductBefore = Product::count();
        $response = $this->from(route('products.create'))
            ->post(route('products.store'), $storeData);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors(array_keys($storeData));
        $this->assertDatabaseCount('products', $countProductBefore);
    }

    public function try_to_store_new_product_with_permission(): void
    {
        $countProductBefore = Product::count();
        $storeData = $this->makeStoreProductData();
        $response = $this->from(route('products.create'))
            ->post(route('products.store'), $storeData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has(
                        'data',
                        fn(AssertableJson $json) => $json
                            ->where('name', $storeData['name'])
                            ->where('description', $storeData['description'])
                            ->etc()
                    )
                    ->etc()
            );
        $this->assertDatabaseCount('products', $countProductBefore + 1);
    }

    public function makeStoreProductData(): array
    {
        $name = 'cat_' . time() . '.jpg';
        $file = UploadedFile::fake()->image($name);
        $storeData = Product::factory()->make()->toArray();
        $storeData['image'] = $file;
        return $storeData;
    }
}
