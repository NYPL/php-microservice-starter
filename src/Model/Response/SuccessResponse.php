<?php
namespace NYPL\Starter\Model\Response;

use NYPL\Starter\APILogger;
use NYPL\Starter\Model;
use NYPL\Starter\Model\Response;
use NYPL\Starter\ModelSet;

abstract class SuccessResponse extends Response
{
    /**
     * @SWG\Property
     */
    public $data;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var int
     */
    public $totalCount;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @param Model|Model[] $model
     * @param int $code
     */
    public function __construct($model = null, $code = 200)
    {
        if ($model) {
            $this->initializeResponse($model);
        }

        $this->setStatusCode($code);
    }

    /**
     * @param Model|Model[]|ModelSet $model
     */
    public function initializeResponse($model)
    {
        $this->setData($model);

        if (is_array($model)) {
            $this->initializeArrayOfModels($model);
        } else if ($model instanceof ModelSet) {
            $this->initializeModelSet($model);
        } else if ($model instanceof Model) {
            $this->initializeModel($model);
        }
    }

    /**
     * @param Model $model
     */
    public function initializeModel($model)
    {
        APILogger::addDebug('Model');
        $this->setCount(1);
    }

    /**
     * @param Model[] $arrayOfModels
     */
    public function initializeArrayOfModels($arrayOfModels)
    {
        APILogger::addDebug('Array Of Models');
        $this->setCount(count($arrayOfModels));
    }

    /**
     * @param ModelSet $modelSet
     */
    public function initializeModelSet($modelSet)
    {
        APILogger::addDebug('ModelSet');
        if ($modelSet->isIncludeTotalCount()) {
            $this->setTotalCount($modelSet->getTotalCount());
        }
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;
    }

    /**
     * @return Model|Model[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Model|Model[] $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }
}
