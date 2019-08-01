<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoContainersTest extends TestCase
{
    /**
     * Test getting optimal containers for photo
     *
     * @return void
     */
    public function testSuccessfulGetOptimalContainers()
    {
//        Artisan::call('db:seed');
        $response = $this->get('/api/containers/get-optimal');

        $data = json_decode($response->getContent());

        $productTypesCount = ProductType::query()->count();

        $result = DB::table('container_product_type')
            ->select(DB::raw('count(DISTINCT product_type_id) as count'))
            ->whereIn('container_id', $data->data)
            ->get();

        $this->assertEquals($productTypesCount, $result->first()->count);
        $this->assertEquals($data->count, count($data->data));

        $response->assertStatus(200);
    }

    public function testSuccessGetAllContainers()
    {
        $response = $this->get('/api/containers/');

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'

        ]);
        $response->assertStatus(200);
    }

    public function testSuccessGetContainer()
    {
        $container = factory(Container::class)->create();

        $response = $this->get('/api/containers/'.$container->id);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'products'
            ],
        ]);
        $response->assertStatus(200);
    }

    public function testGetNonExistContainer()
    {
        $response = $this->get('/api/containers/-1');

        $response->assertJsonStructure([
            'error'
        ]);
        $response->assertStatus(404);
    }

    public function testSuccessDestroyContainer()
    {
        $container = factory(Container::class)->create();

        $response = $this->delete('/api/containers/'.$container->id);

        $response->assertJsonStructure([
            'data'
        ]);
        $response->assertStatus(200);
    }

    public function testDestroyNonExistContainer()
    {
        $response = $this->delete('/api/containers/-1');

        $response->assertJsonStructure([
            'error'
        ]);
        $response->assertStatus(404);
    }

    public function testSuccessCreateContainer()
    {
        $products = Product::select('id')->limit(10)->get();
        $response = $this->post('/api/containers/', [
            'name' => 'test container',
            'products' =>$products->toArray()
        ]);

        $response->assertJsonStructure([
            'data'
        ]);
        $response->assertStatus(201);
    }

    public function testValidationErrorCreateContainer()
    {
        $response = $this->post('/api/containers/', [

        ]);

        $response->assertJsonStructure([
            'error'
        ]);
        $response->assertStatus(422);
    }
}
