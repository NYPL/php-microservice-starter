<?php
namespace NYPL\API\Model\ModelTrait;

use NYPL\API\APIException;
use NYPL\API\Model;

trait TranslateTrait
{
    /**
     * @param mixed $data
     * @param bool $decodeJson
     * @param bool $validateData
     */
    public function __construct($data = null, $decodeJson = false, $validateData = false)
    {
        if ($decodeJson && is_string($data)) {
            $data = json_decode($data, true);
        }

        if ($data) {
            $this->translate($data, $validateData);
        }
    }

    /**
     * @param array $data
     * @param bool $validateData
     */
    public function translate(array $data = [], $validateData = false)
    {
        foreach ($data as $objectKey => $objectValue) {
            if (isset($objectValue)) {
                $this->setObject($objectKey, $objectValue, $validateData);
            }
        }
    }

    /**
     * @param string $objectKey
     * @param mixed $objectValue
     * @param bool $validateData
     *
     * @return bool
     * @throws APIException
     */
    protected function setObject($objectKey, $objectValue, $validateData = false)
    {
        $translator = "translate{$objectKey}";
        $setter = "set{$objectKey}";

        if (method_exists($this, $translator) && method_exists($this, $setter)) {
            $this->$setter($this->$translator($objectValue));

            return true;
        }

        if (method_exists($this, $setter)) {
            $this->$setter($objectValue);

            return true;
        }

        if ($validateData) {
            if ($objectKey == 'id') {
                return true;
            }

            throw new APIException('Data attribute (' . $objectKey . ') specified is not valid');
        }
    }

    /**
     * @param array|string $data
     * @param Model $model
     * @param bool $decodeJson
     *
     * @return Model[]
     */
    protected function translateArray($data, Model $model, $decodeJson = false)
    {
        if ($decodeJson && is_string($data)) {
            $data = json_decode($data, true);
        }

        $modelArray = [];

        foreach ($data as $value) {
            /**
             * @var TranslateTrait $newModel
             */
            $newModel = clone $model;
            $newModel->translate($value);

            $modelArray[] = $newModel;
        }

        return $modelArray;
    }


    /**
     * @param Model $newModel
     */
    public function translateFromNewModel(Model $newModel)
    {
        foreach (get_object_vars($newModel) as $objectName => $objectValue) {
            $setter = "set{$objectName}";

            if (isset($objectValue) && method_exists($this, $setter)) {
                $this->$setter($objectValue);
            }
        }
    }
}
