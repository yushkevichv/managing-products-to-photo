<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //disable foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('product_types')->truncate();
        DB::table('containers')->truncate();
        DB::table('product_container')->truncate();
        DB::table('container_product_type')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // make product types and generate equal number products for each
        factory(App\Models\ProductType::class, (int) \Config::get('app.product_type_count'))
            ->create()
            ->each(function ($productType) {
                $now = now();
                $products = factory(App\Models\Product::class, (int) \Config::get('app.products_by_type_count'))->make([
                    'type_id' => $productType->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                \App\Models\Product::insert($products->toArray());
            });

        // get all products, shuffle for random and chunked for pack at containers
        $products = \App\Models\Product::all()->shuffle();
        $chunks = $products->chunk(\Config::get('app.product_count_at_container'));

        // create container for every chunk and attach products to container
        factory(App\Models\Container::class, $chunks->count())
            ->create()
            ->each(function ($container) use ($chunks)  {
                // @todo fix calculate shift chunks if we want call seedeer at additional
                // if we need to generate products
                $container->products()->attach($chunks[$container->id -1]->pluck('id'));
                $container->product_types()->attach($chunks[$container->id -1]->pluck('type_id')->unique());
            });
    }
}
