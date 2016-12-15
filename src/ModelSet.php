<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\ModelInterface\ReadInterface;
use NYPL\Starter\Model\ModelTrait\DBTrait;
use NYPL\Starter\Model\ModelTrait\DBReadTrait;

class ModelSet extends Model implements ReadInterface
{
    use DBTrait, DBReadTrait;

    /**
     * @var Model
     */
    protected $baseModel;

    /**
     * @var Model[]
     */
    public $data;

    /**
     * @var string|OrderBy[]
     */
    public $orderBy = "";

    /**
     * @var string
     */
    public $orderDirection = "";

    /**
     * @var int
     */
    public $offset = 0;

    /**
     * @var int
     */
    public $limit = 25;

    /**
     * @var bool
     */
    public $noDefaultSorting = false;

    /**
     * @param Model $baseModel
     * @param bool $noDefaultSorting
     */
    public function __construct(Model $baseModel, $noDefaultSorting = false)
    {
        $this->setBaseModel($baseModel);
        $this->setNoDefaultSorting($noDefaultSorting);
    }

    /**
     * @return Model
     */
    public function getBaseModel()
    {
        return $this->baseModel;
    }

    /**
     * @param Model $baseModel
     */
    public function setBaseModel(Model $baseModel)
    {
        $this->baseModel = $baseModel;
    }

    /**
     * @param Model $model
     */
    public function addModel(Model $model)
    {
        $this->data[] = $model;
    }

    /**
     * @return \NYPL\Starter\Model[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \NYPL\Starter\Model[] $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string|OrderBy[] $orderBy
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string|OrderBy[]
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     */
    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        if ($limit > 0) {
            $this->limit = (int) $limit;
        }
    }

    /**
     * @return boolean
     */
    public function isNoDefaultSorting()
    {
        return $this->noDefaultSorting;
    }

    /**
     * @param boolean $noDefaultSorting
     */
    public function setNoDefaultSorting($noDefaultSorting)
    {
        $this->noDefaultSorting = (bool) $noDefaultSorting;
    }
}
