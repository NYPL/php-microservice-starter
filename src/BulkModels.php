<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\BulkError;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\ModelTrait\DBCreateTrait;
use NYPL\Starter\Model\ModelTrait\MessageTrait;

class BulkModels
{
    use MessageTrait;

    /**
     * @var Model[]
     */
    public $models = [];

    /**
     * @var Model[]
     */
    public $successModels = [];

    /**
     * @var BulkError[]
     */
    public $bulkErrors = [];

    /**
     * @var bool
     */
    public $publishMessages = false;

    /**
     * @return Model[]
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param Model[] $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * @param Model $model
     */
    public function addModel(Model $model)
    {
        $model->setBulk(true);

        $this->models[] = $model;
    }

    /**
     * @return BulkError[]
     */
    public function getBulkErrors()
    {
        return $this->bulkErrors;
    }

    /**
     * @param BulkError[] $bulkErrors
     */
    public function setBulkErrors($bulkErrors)
    {
        $this->bulkErrors = $bulkErrors;
    }

    /**
     * @param BulkError $bulkError
     */
    public function addBulkError(BulkError $bulkError)
    {
        $this->bulkErrors[] = $bulkError;
    }

    /**
     * @return bool
     */
    public function isPublishMessages()
    {
        return $this->publishMessages;
    }

    /**
     * @param bool $publishMessages
     */
    public function setPublishMessages($publishMessages)
    {
        $this->publishMessages = (bool) $publishMessages;
    }

    /**
     * @return Model[]
     */
    public function getSuccessModels()
    {
        return $this->successModels;
    }

    /**
     * @param Model[] $successModels
     */
    public function setSuccessModels($successModels)
    {
        $this->successModels = $successModels;
    }

    /**
     * @param Model $model
     */
    public function addSuccessModel(Model $model)
    {
        $this->successModels[] = $model;
    }

    /**
     * @param bool $useId
     * @throws \InvalidArgumentException|\AvroIOException|APIException
     */
    public function create($useId = false)
    {
        if (!$this->getModels()) {
            throw new APIException(
                'No records provided for create operation.',
                null,
                0,
                null,
                400
            );
        }

        /**
         * @var $model Model|DBCreateTrait
         */
        foreach ($this->getModels() as $model) {
            if ($this instanceof MessageInterface) {
                $this->setPublishMessages(true);
            }

            $model->create($useId);

            $this->addSuccessModel($model);
        }

        $this->bulkPublishMessages(
            $this->getSuccessModels()
        );
    }
}
