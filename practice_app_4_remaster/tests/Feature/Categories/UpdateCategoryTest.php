<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\AbstractMiddlewareTestCase;

class UpdateCategoryTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function admin_can_updated_category()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-update');
        $data = $this->createData();
        $dataUpdate = $this->makeData();
        $response = $this->put($this->getRoute($data->id), $dataUpdate);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data', fn (AssertableJson $json) => $json
                    ->where('name', $dataUpdate['name'])
                    ->etc()
                )
                ->etc()
        );
    }

    /** @test */
    public function cannot_update_with_invalid_data()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-update');
        $data = $this->createData();
        $dataUpdate = $this->makeStupidData();
        $response = $this->from($this->getEditViewRoute($data->id))->put($this->getRoute($data->id), $dataUpdate);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function can_not_update_category_if_unauthenticated()
    {
        $data = $this->createData();
        $dataUpdate = $this->makeData();
        $response = $this->put($this->getRoute($data->id), $dataUpdate);
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_not_update_if_category_not_exist()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-update');
        $dataUpdate = $this->makeData();
        $response = $this->put($this->getRoute(-1), $dataUpdate);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function non_admin_user_can_not_update_category()
    {
        $this->testAsNewUser();
        $data = $this->createData();
        $dataUpdate = $this->makeData();
        $response = $this->put($this->getRoute($data->id), $dataUpdate);
        $response->assertSessionHas(config('constants.AUTHENTICATION_ERROR_KEY'));
    }

    public function createData()
    {
        return Category::factory()->create();
    }

    public function makeData()
    {
        return Category::factory()->make()->toArray();
    }

    public function makeStupidData()
    {
        $data = Category::factory()->make()->toArray();
        $data['name'] = '';
        return $data;
    }

    public function getEditViewRoute($id)
    {
        return route('categories.edit', $id);
    }

    public function getRoute($id)
    {
        return route('categories.update', $id);
    }

    public function getIndexRoute()
    {
        return route('categories.index');
    }

    public function getTableName()
    {
        return 'categories';
    }
}