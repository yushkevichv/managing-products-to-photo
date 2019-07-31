<?php


namespace App\Repositories;


use App\Models\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PhotoContainerRepository
{
    /**
     * use just if need more data.
     * not using for current task
     * @return Collection
     */
    public function getContainersWithTypedProducts() : Collection
    {
        $result = [];

        $containers = Container::query()
            ->select('containers.id as container_id', 'container_product_type.product_type_id as type_id')
            ->join('container_product_type', 'containers.id', '=', 'container_product_type.container_id')
            ->get();

        $containers = array_reduce($containers->toArray(), function (array $data, array $element) {
            $data[$element['container_id']][] = $element;
            return $data;
        }, []);

        foreach ($containers as $containerId => $container) {
            $uniqueTypeId = array_unique(array_column($container, 'type_id'));
            $result[$containerId]= [
                'container_id' => $containerId,
                'product_id' => array_column($container, 'product_id'),
                'type_id' => array_values($uniqueTypeId),
                'unique_count' => count($uniqueTypeId),
            ];
        }

        return collect($result);
    }

    public function getContainersAndProductTypes() : Collection
    {
        $result = [];

        $containers = DB::table('container_product_type')
            ->select('container_product_type.container_id as container_id', 'container_product_type.product_type_id as type_id')
            ->get();

        $containers = array_reduce($containers->toArray(), function ( $data, $element) {
            $data[$element->container_id][] = (array) $element;
            return $data;
        }, []);

        foreach ($containers as $containerId => $container) {
            $uniqueTypeId = array_unique(array_column($container, 'type_id'));
            $result[$containerId]= [
                'container_id' => $containerId,
                'type_id' => array_values($uniqueTypeId),
                'unique_count' => count($uniqueTypeId),
            ];
        }

        return collect($result);
    }
}
