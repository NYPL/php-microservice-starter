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
     * @var bool
     */
    public $andFilters = false;

    /**
     * @param Filter[] $filters
     * @param bool $andFilters
     */
    public function __construct(array $filters = [], $andFilters = false)
    {
        if ($filters) {
            $this->setFilters($filters);
        }

        if ($andFilters) {
            $this->setAndFilters($andFilters);
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
    public function setFilters(array $filters)
    {
        $filters = array_filter($filters, function ($element) {
            if ($element instanceof Filter) {
                return true;
            }
        });

        $this->filters = $filters;
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return bool
     */
    public function isAndFilters()
    {
        return $this->andFilters;
    }

    /**
     * @param bool $andFilters
     */
    public function setAndFilters($andFilters)
    {
        $this->andFilters = $andFilters;
    }
}
