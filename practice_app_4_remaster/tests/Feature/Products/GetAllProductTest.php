<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Tests\Feature\AbstractMiddlewareTestCase;

class GetAllProductTest extends AbstractMiddlewareTestCase
{
    /**
     * @test
     */
    public function user_can_get_all_products(): void
    {
        $this->testAsUser();
        $this->withoutExceptionHandling();
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.products.index');
        $response->assertViewHas('categories');
    }
}
