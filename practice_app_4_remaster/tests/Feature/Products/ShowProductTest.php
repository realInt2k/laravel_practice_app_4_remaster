<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class ShowProductTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_a_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->get($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    public function getRoute(int $id): string
    {
        return route('products.show', $id);
    }

    /** @test */
    public function cannot_see_product_with_invalid_id(): void
    {
        $this->loginAsNewUser();
        $id = -1;
        $response = $this->get($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function everyone_can_see_product_with_valid_id(): void
    {
        $this->withoutExceptionHandling();
        $this->loginAsNewUser();
        $product = Product::factory()
            ->withRandomPhoto()
            ->withRandomCategory()
            ->create();
        $response = $this->get($this->getRoute($product->id));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->where('data', fn($data)
                    => !empty($data)
                    && str_contains($data, $product->imagePath)
                )
                ->etc()
        )
            ->assertSee([
                $product->name,
                $product->description
            ])
            ->assertSee(
                $product->categories()->pluck('name')->toArray()
            );
    }
}
