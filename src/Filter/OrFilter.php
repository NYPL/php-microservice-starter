<?php
namespace NYPL\Starter\Filter;

use NYPL\Starter\Filter;

class OrFilter extends Filter
{
    /**
     * @var Filter[]
     */
    public $filters = [];

    /**
     * @param Filter[] $filters
     */
    public function __construct(array $filters = [])
    {
        if ($filters) {
            $this->setFilters($filters);
        }
    }

    /**
     * @return \NYPL\Starter\Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param \NYPL\Starter\Filter[] $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }
}
