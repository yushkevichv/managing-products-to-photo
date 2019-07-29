<?php


namespace App\Repositories;


use App\Models\Container;
use Illuminate\Support\Collection;

class PhotoContainerRepository
{
    public function getContainersWithTypedProducts() : Collection
    {
        $result = [];
        $timeStart = microtime(true);

//        $containers = Container::query()
//            ->select('containers.id as container_id', 'products.type_id')
//            ->join('product_container', 'containers.id', '=', 'product_container.container_id')
//            ->join('products', 'products.id', '=', 'product_container.product_id')
////            ->toSql();
////        dd($containers);
//            ->get();

        $containers = Container::query()
            ->select('containers.id as container_id', 'container_product_type.product_type_id as type_id')
            ->join('container_product_type', 'containers.id', '=', 'container_product_type.container_id')
//            ->join('products', 'products.id', '=', 'product_container.product_id')
//            ->toSql();
//        dd($containers);
            ->get();

        $containers = array_reduce($containers->toArray(), function (array $data, array $element) {
            $data[$element['container_id']][] = $element;
            return $data;
        }, []);
        echo 'time sql: '.(microtime(true) - $timeStart );
        $timeStart = microtime(true);

        foreach ($containers as $containerId => $container) {
            $result[$containerId]= [
                'container_id' => $containerId,
                'product_id' => array_column($container, 'product_id'),
                'type_id' => array_values(array_unique(array_column($container, 'type_id'))),
                'unique_count' => count(array_unique(array_column($container,'type_id'))),
            ];
        }
        echo 'time php: '.(microtime(true) - $timeStart );

        return collect($result);
    }

}
