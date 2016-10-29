<?php
namespace NYPL\Starter\Model\Response;

use NYPL\Starter\Model;
use NYPL\Starter\Model\Response;

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
     * @param Model|Model[] $model
     */
    public function initializeResponse($model)
    {
        $this->setData($model);

        if (is_array($model)) {
            $this->setCount(count($model));
        } else {
            $this->setCount(1);
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
}
