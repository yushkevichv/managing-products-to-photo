<?php

namespace Tests\Feature;

use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoContainersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSuccessfulGetContainers()
    {
        $response = $this->get('/api/containers/get');

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
}
