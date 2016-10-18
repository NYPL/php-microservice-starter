<?php
namespace NYPL\API;

use NYPL\API\Model\LocalDateTime;
use NYPL\API\Model\ModelTrait\DBReadTrait;
use NYPL\API\Model\ModelTrait\DBTrait;

abstract class Model implements \JsonSerializable
{
    use DBTrait, DBReadTrait;

    /**
     * @var Filter[]
     */
    public $filters;

    /**
     * @var array
     */
    public $excludeProperties = ['filters', 'excludeProperties'];

    protected function getJsonObjectValue($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format("c");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE_TIME) {
            return $value->getDateTime()->format("Y-m-d H:i:s");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE) {
            return $value->getDateTime()->format("Y-m-d");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE_TIME_RFC) {
            return $value->getDateTime()->format("c");
        }

        return $value;
    }

    public function jsonSerialize()
    {
        $jsonArray = [];

        foreach (get_object_vars($this) as $objectName => $objectValue) {
            if (!in_array($objectName, $this->getExcludeProperties())) {
                $jsonArray[$objectName] = $this->getJsonObjectValue($objectValue);
            }
        }

        return $jsonArray;
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filter[] $filters
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
        if ($filter->getFilterValue()) {
            $this->filters[] = $filter;
        }
    }

    /**
     * @return array
     */
    public function getExcludeProperties()
    {
        return $this->excludeProperties;
    }

    /**
     * @param array $excludeProperties
     */
    public function setExcludeProperties($excludeProperties)
    {
        $this->excludeProperties = $excludeProperties;
    }
}
