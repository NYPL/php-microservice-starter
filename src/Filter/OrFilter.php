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
     * @var string
     */
    public $chainType;

    /**
     * @param Filter[] $filters
     */
    public function __construct(array $filters = [], $chainType = 'OR')
    {
        if ($filters) {
            $this->setFilters($filters);
        }
        if ($chainType) {
            $this->setChainType($chainType);
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

    /**
     * @return string
     */
    public function getChainType()
    {
        return $this->chainType;
    }

    /**
     * @param string $chainType
     */
    public function setChainType($chainType)
    {
        $this->chainType = $chainType;
    }
}
