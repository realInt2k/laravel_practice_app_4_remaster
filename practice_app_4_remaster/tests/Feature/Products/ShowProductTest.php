<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class ShowProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function cannot_see_product_with_invalid_id(): void
    {
        $this->testAsUser();
        $id = -1;
        $response = $this->get(route('products.show', $id));
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_see_product_with_valid_id(): void
    {
        $this->testAsUser();
        $product = Product::factory()->create();
        $id = -1;
        $response = $this->get(route('products.show', $product->id));
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'html',
                )
                ->etc()
        );
        $response->assertSee($product->name);
        $response->assertSee($product->description);
    }
}
