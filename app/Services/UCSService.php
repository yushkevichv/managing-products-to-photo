<?php


namespace App\Services;


use Illuminate\Support\Collection;

class UCSService
{
    protected $graph;
    protected $data;
    protected $accumProductTypes;
    protected $containers;
    protected $start;

    public function __construct()
    {
        $this->containers = collect([]);
    }

    public function initGraph(Collection $data)
    {
        $initStart = $this->getInitStart($data);
        $this->start = $initStart['container_id'];
        $this->accumProductTypes = $initStart['type_id'];
        $this->containers = collect($this->start);
        $this->data = $data;
        $this->calculateCost();
    }

    public function getInitStart(Collection $data) : array
    {
        $max = $data->max('unique_count');
        return $data->where('unique_count', $max)->first();
    }

    public function calculateCost()
    {
        $graph = [];
        $data = $this->data->whereNotIn('container_id', $this->containers);

        $countAccumProductTypes = count($this->accumProductTypes);
        foreach ($data as $key => $value) {
            $graph[$key] = $this->getCost($this->accumProductTypes, $value, $countAccumProductTypes);
        }
        $this->graph = collect($graph);
    }

    public function getCost(array $a, array $b, $countStart) : int
    {
        $count = count(array_unique(
            array_merge($a, $b['type_id'])
        )) - $countStart;
        return $count;
    }

    public function getMinContainers()
    {
        $needRecalc = true;
        while($needRecalc) {
            $nextContainer = $this->graph->sort()->keys()->last();
            if(!$nextContainer || ($this->graph[$nextContainer] == 0))  {
                $needRecalc = false;
                break;
            }

            $this->containers->push($nextContainer);
            $this->accumProductTypes = array_values(
                array_unique(
                    array_merge($this->accumProductTypes, $this->data->toArray()[$nextContainer]['type_id'])
                )
            );

            $this->calculateCost();
        }

        return $this->containers;
    }
}
