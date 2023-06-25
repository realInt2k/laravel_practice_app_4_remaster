<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestCaseUtils;

class ShowCategoryTest extends TestCaseUtils
{
    /** @test */
    public function unauthenticated_cannot_see_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function everyone_can_see_a_category()
    {
        $this->loginAsNewUser();
        $category = Category::factory()->create();
        $response = $this->get($this->getRoute($category->id));
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->where('data', fn ($data) => !empty($data))
                    ->etc()
            )
            ->assertSee($category->name);
    }

    /** @test */
    public function cannot_see_category_with_invalid_id()
    {
        $this->loginAsNewUser();
        $id = -1;
        $response = $this->get($this->getRoute($id));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function getRoute(int $id): string
    {
        return route('categories.show', $id);
    }
}
