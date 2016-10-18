<?php
namespace NYPL\Starter;

class Filter
{
    /**
     * @var string
     */
    public $filterColumn = '';

    /**
     * @var mixed
     */
    public $filterValue;

    /**
     * @var bool
     */
    public $jsonColumn = false;

    /**
     * @var string
     */
    public $id = '';

    /**
     * @param string $filterColumn
     * @param mixed $filterValue
     * @param bool $isJsonColumn
     * @param string $id
     */
    public function __construct($filterColumn = '', $filterValue = '', $isJsonColumn = false, $id = '')
    {
        if ($filterColumn && $filterColumn) {
            $this->setFilterColumn($filterColumn);
            $this->setFilterValue($filterValue);
            $this->setJsonColumn($isJsonColumn);
        }

        if ($id) {
            $this->setId($id);
        }
    }

    /**
     * @return string
     */
    public function getFilterColumn()
    {
        return $this->filterColumn;
    }

    /**
     * @param string $filterColumn
     */
    public function setFilterColumn($filterColumn)
    {
        $this->filterColumn = $filterColumn;
    }

    /**
     * @return mixed
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * @param mixed $filterValue
     */
    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;
    }

    /**
     * @return bool
     */
    public function isJsonColumn()
    {
        return $this->jsonColumn;
    }

    /**
     * @param bool $jsonColumn
     */
    public function setJsonColumn($jsonColumn)
    {
        $this->jsonColumn = (bool) $jsonColumn;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
