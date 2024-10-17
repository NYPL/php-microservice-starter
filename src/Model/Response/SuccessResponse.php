<?php
namespace NYPL\Starter\Model\Response;

use NYPL\Starter\Model;
use NYPL\Starter\ModelSet;
use NYPL\Starter\Model\Response;

abstract class SuccessResponse extends Response
{
    /**
     * @OA\Property
     */
    public $data;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var int
     */
    public $totalCount = 0;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @param Model|Model[]|ModelSet $model
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
     * @return bool
     */
    public function initializeResponse($model)
    {
        // ModelSet needs to be checked first to avoid
        // returning baseModel data in response
        if ($model instanceof ModelSet) {
            $this->initializeModelSet($model);
            return true;
        }

        if ($model instanceof Model) {
            $this->initializeModel($model);
            return true;
        }

        if (is_array($model)) {
            $this->initializeArrayOfModels($model);
            return true;
        }
    }

    /**
     * @param Model $model
     */
    public function initializeModel($model)
    {
        $this->setData($model);
        $this->setCount(1);
    }

    /**
     * @param Model[] $arrayOfModels
     */
    public function initializeArrayOfModels($arrayOfModels)
    {
        $this->setData($arrayOfModels);
        $this->setCount(count($arrayOfModels));
    }

    /**
     * @param ModelSet $modelSet
     */
    public function initializeModelSet($modelSet)
    {
        $this->setData($modelSet->getData());
        $this->setCount(count($modelSet->getData()));
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
