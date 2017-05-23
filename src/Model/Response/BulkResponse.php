<?php
namespace NYPL\Starter\Model\Response;

use NYPL\Starter\Model;
use NYPL\Starter\Model\Response;
use NYPL\Starter\Model\BulkError;

abstract class BulkResponse extends Response
{
    /**
     * @SWG\Property
     */
    public $data;

    /**
     * @SWG\Property(example=1, type="int")
     * @var int
     */
    public $count = 0;

    /**
     * @SWG\Property()
     * @var BulkError[]
     */
    public $errors;

    /**
     * @SWG\Property(example=200, type="int")
     * @var int
     */
    public $statusCode;

    /**
     * @param Model[] $successModels
     * @param BulkError[] $errors
     * @param int $code
     */
    public function __construct(array $successModels = [], array $errors = [], $code = 200)
    {
        $this->initializeResponse($successModels, $errors);

        $this->setStatusCode($code);
    }

    /**
     * @param Model[] $successModels
     * @param BulkError[] $errors
     */
    public function initializeResponse($successModels, $errors)
    {
        $this->setData($successModels);
        $this->setErrors($errors);

        if ($successModels) {
            $this->setCount(count($successModels));
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
     * @return Model[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Model[] $data
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
     * @return BulkError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param BulkError[] $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
