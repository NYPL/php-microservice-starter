<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\LocalDateTime;

abstract class Model implements \JsonSerializable
{
    /**
     * @var Filter[]
     */
    public $filters;

    /**
     * @var array
     */
    public $excludeProperties = ['filters', 'excludeProperties', 'rawData', 'bulk', 'addNullToJson', 'topic', 'streamName'];

    /**
     * @var array
     */
    public $rawData = [];

    /**
     * @var bool
     */
    public $bulk = false;

    /**
     * @var bool
     */
    public $addNullToJson = true;

    /**
     * @param $value
     * @return string
     */
    public function getJsonObjectValue($value)
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

    /**
     * @param string $objectName
     *
     * @return mixed|string
     */
    protected function getJsonObjectName($objectName = '')
    {
        return $objectName;
    }

    /**
     * @param string $objectName
     * @param mixed $objectValue
     *
     * @return bool
     */
    protected function addToJsonArray($objectName, $objectValue)
    {
        if (in_array($objectName, $this->getExcludeProperties())) {
            return false;
        }

        if ($objectValue === null && !$this->isAddNullToJson()) {
            return false;
        }

        return true;
    }

    public function jsonSerialize()
    {
        $jsonArray = [];

        foreach (get_object_vars($this) as $objectName => $objectValue) {
            if ($this->addToJsonArray($objectName, $objectValue)) {
                $jsonArray[$this->getJsonObjectName($objectName)] = $this->getJsonObjectValue($objectValue);
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
        $this->filters[] = $filter;
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

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param array $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return bool
     */
    public function isBulk()
    {
        return $this->bulk;
    }

    /**
     * @param bool $bulk
     */
    public function setBulk($bulk)
    {
        $this->bulk = (bool) $bulk;
    }

    /**
     * @return bool
     */
    public function isAddNullToJson()
    {
        return $this->addNullToJson;
    }

    /**
     * @param bool $addNullToJson
     */
    public function setAddNullToJson($addNullToJson)
    {
        $this->addNullToJson = (bool) $addNullToJson;
    }
}
