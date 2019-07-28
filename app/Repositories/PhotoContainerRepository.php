<?php


namespace App\Repositories;


use App\Models\Container;

class PhotoContainerRepository
{
    public function getContainersWithTypedProducts()
    {
        $result = [];

        $containers = Container::query()
            ->select('containers.id as container_id', 'products.id as product_id', 'products.type_id')
            ->join('product_container', 'containers.id', '=', 'product_container.container_id')
            ->join('products', 'products.id', '=', 'product_container.product_id')
            ->get()
            ->groupBy('container_id');

        foreach ($containers as $containerId => $container) {
            $result = [
                'container_id' => $containerId,
                'product_id' => $container->pluck('product_id')->toArray(),
                'type_id' => $container->pluck('type_id')->unique()->values()->toArray(),
                'unique_count' => $container->pluck('type_id')->unique()->count(),
            ];
        }
        return $result;
    }

}
