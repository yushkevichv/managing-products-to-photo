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
        // make product types and generate equal number products for each
        factory(App\Models\ProductType::class, 100)
            ->create()
            ->each(function ($productType) {
                $now = now();
                $products = factory(App\Models\Product::class, 100)->make([
                    'type_id' => $productType->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                \App\Models\Product::insert($products->toArray());
            });

        // get all products, shuffle for random and chunked for pack at containers
        $products = \App\Models\Product::all()->shuffle();
        $chunks = $products->chunk(100);

        // create container for every chunk and attach products to container
        factory(App\Models\Container::class, $chunks->count())
            ->create()
            ->each(function ($container) use ($chunks)  {
                // if we need to generate products
                $container->products()->attach($chunks[$container->id -1]->pluck('id'));
                $container->product_types()->attach($chunks[$container->id -1]->pluck('type_id')->unique());
            });
    }
}
