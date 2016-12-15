<?php
namespace NYPL\Starter;

class OrderBy
{
    public $column = '';

    public $direction = '';

    /**
     * @param string $column
     * @param string $direction
     */
    public function __construct($column = '', $direction = '')
    {
        if ($column) {
            $this->setColumn($column);
        }

        if ($this->getDirection()) {
            $this->setDirection($direction);
        }
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param string $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
}
