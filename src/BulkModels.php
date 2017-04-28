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
     * @throws \InvalidArgumentException|\AvroIOException
     */
    public function create($useId = false)
    {
        /**
         * @var $model Model|DBCreateTrait
         */
        foreach ($this->getModels() as $count => $model) {
            try {
                if ($this instanceof MessageInterface) {
                    $this->setPublishMessages(true);
                }

                $model->create($useId);

                $this->addSuccessModel($model);
            } catch (\Exception $exception) {
                $this->addBulkError(new BulkError(
                    $count,
                    $exception->getMessage(),
                    $model->getRawData()
                ));
            }
        }

        $this->bulkPublishMessages(
            $this->getSuccessModels()
        );
    }
}
