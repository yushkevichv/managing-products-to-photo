<?php


namespace App\Services;


use Illuminate\Support\Collection;

class UCSService
{
    public function getInitStart(Collection $data) : array
    {
        $max = $data->max('unique_count');
        return $data->where('unique_count', $max)->first();
    }

    public function initGraph($data)
    {
        $start = $this->getInitStart($data);
        $graph = [];
        foreach ($data as $key => $value) {
            if($start['container_id'] == $key) {
                continue;
            }

            $graph[$start['container_id']][$key] = $this->getCost($start, $value);
        }
        return $graph;
    }

    public function getCost(array $a, array $b) : int
    {
        return count(array_diff($a['type_id'], $b['type_id']));
    }

}
