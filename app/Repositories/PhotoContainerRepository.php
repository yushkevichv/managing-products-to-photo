<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PhotoContainerRepository
{
    /**
     * get containers and product type in each container.
     * Grouped by container
     *
     * @return Collection
     */
    public function getContainersAndProductTypes(): Collection
    {
        $result = [];

        $containers = DB::table('container_product_type')
            ->select('container_product_type.container_id as container_id',
                'container_product_type.product_type_id as type_id')
            ->get();

        $containers = array_reduce($containers->toArray(), function ($data, $element) {
            $data[$element->container_id][] = (array) $element;
            return $data;
        }, []);

        foreach ($containers as $containerId => $container) {
            $uniqueTypeId = array_unique(array_column($container, 'type_id'));
            $result[$containerId] = [
                'container_id' => $containerId,
                'type_id' => array_values($uniqueTypeId),
                'unique_count' => count($uniqueTypeId),
            ];
        }

        return collect($result);
    }
}
