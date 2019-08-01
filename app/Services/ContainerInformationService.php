<?php


namespace App\Services;


use Illuminate\Support\Collection;

final class ContainerInformationService
{
    /**
     * Store current version of graph
     * @var Collection
     */
    protected $graph;

    /**
     * Store init data from repository
     * @var Collection
     */
    protected $data;

    /**
     * Store current version of unique array different product types
     * @var array
     */
    protected $accumulateProductTypes;

    /**
     * stack for storing container ids
     * @var Collection
     */
    protected $containers;

    /**
     * store start containerId
     * @var int
     */
    protected $start;

    public function __construct()
    {
        $this->containers = collect([]);
    }

    /**
     * Calculate init params. Init graph with start node and cost
     *
     * @param  Collection  $data
     */
    private function initGraph(Collection $data) : void
    {
        $initStart = $this->getInitStart($data);
        $this->start = $initStart['container_id'];
        $this->accumulateProductTypes = $initStart['type_id'];
        $this->containers = collect($this->start);
        $this->data = $data;
        $this->calculateCost();
    }

    /**
     * get start node how max count unique product types
     *
     * @param  Collection  $data
     * @return array
     */
    private function getInitStart(Collection $data): array
    {
        $max = $data->max('unique_count');
        return $data->where('unique_count', $max)->first();
    }

    /**
     * skip containers in stack for optimisation.
     * Calculate cost for all V
     */
    private function calculateCost() : void
    {
        $graph = [];
        $data = $this->data->whereNotIn('container_id', $this->containers);

        $countAccumulateProductTypes = count($this->accumulateProductTypes);
        foreach ($data as $key => $value) {
            $graph[$key] = $this->getCost($this->accumulateProductTypes, $value, $countAccumulateProductTypes);
        }
        $this->graph = collect($graph);
    }

    /**
     * countStart should be equal count($a). Added for optimistaion calculation in cycle.
     *
     * @param  array  $a
     * @param  array  $b
     * @param $countStart
     * @return int
     */
    private function getCost(array $a, array $b, $countStart): int
    {
        $count = count(array_unique(
                array_merge($a, $b['type_id'])
            )) - $countStart;
        return $count;
    }

    /**
     * Calculate init data and start node. Start node is max of count unique product types
     * Calculate cost of each V: count new unique product types
     * Add max. Iterate while diff = 0 or we check all containers.
     * It means, we reach goal and have min containers with all different product types
     *
     * @return Collection
     */
    public function getMinContainers($data)
    {
        $this->initGraph($data);

        while (true) {
            $nextContainer = $this->graph->sort()->keys()->last();
            if (!$nextContainer || ($this->graph[$nextContainer] == 0)) {
                break;
            }

            $this->containers->push($nextContainer);
            $this->accumulateProductTypes = array_values(
                array_unique(
                    array_merge($this->accumulateProductTypes, $this->data->toArray()[$nextContainer]['type_id'])
                )
            );

            $this->calculateCost();
        }

        return $this->containers;
    }
}
