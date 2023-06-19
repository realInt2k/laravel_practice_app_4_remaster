<?php

namespace Tests\Feature\categories;

use App\Models\Category;
use Illuminate\Http\Response;
use Tests\Feature\AbstractMiddlewareTestCase;

class DestroyCategoryTest extends AbstractMiddlewareTestCase
{
    /** @test */
    public function admin_can_deleted_category()
    {
        $this->testAsNewUserWithRolePermission('admin', 'categories-destroy');
        $data = $this->createData();
        $dataCount = $this->getDataCount();
        $response = $this->delete($this->getRoute($data->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertEquals($dataCount - 1, $this->getDataCount());
        $this->assertDatabaseMissing('categories', ['id' => $data->id]);
    }

    /** @test */
    public function can_not_delete_category_if_unauthenticated()
    {
        $data = $this->createData();
        $response = $this->delete($this->getRoute($data->id));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_cannot_delete_category()
    {
        $this->testAsNewUser();
        $data = $this->createData();
        $response = $this->delete($this->getRoute($data->id));
        $response->assertStatus(302);
        $response->assertSessionHas(config('constants.authenticationErrorKey'));
    }

    public function createData()
    {
        return Category::factory()->create();
    }

    public function getRoute($id)
    {
        return route('categories.destroy', $id);
    }

    public function getIndexRoute()
    {
        return route('categories.index');
    }

    public function getDataCount()
    {
        return Category::count();
    }
}