<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Model;
use Stringy\Stringy;

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
            $this->setRawData($data);

            $this->translate($data, $validateData);
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function translateToObjectName($name = '')
    {
        return (string) Stringy::create($name)->camelize();
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function translateDbName($key = "")
    {
        $key = (string) Stringy::create($key)->underscored();

        return $key;
    }

    /**
     * @param array $data
     * @param bool $validateData
     */
    public function translate(array $data = [], $validateData = false)
    {
        foreach ($data as $objectKey => $objectValue) {
            if (isset($objectValue)) {
                $this->setObject(
                    $this->translateToObjectName($objectKey),
                    $objectValue,
                    $validateData
                );
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

            throw new APIException("Data attribute (" . $objectKey . ") specified is not valid");
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

        foreach ($data as $key => $value) {
            if ($value) {
                /**
                 * @var TranslateTrait $newModel
                 */
                $newModel = clone $model;
                $newModel->translate($value);

                $modelArray[(int) $key] = $newModel;
            }
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

    /**
     * @param bool $useId
     * @param array $data
     *
     * @return array
     * @throws APIException
     */
    protected function getValueArray($useId = false, array $data = [])
    {
        if (!$data) {
            throw new APIException('No data was supplied for operation');
        }

        $insertValues = [];

        /**
         * @var Model $this
         */
        foreach ($data as $key => $value) {
            if (($useId || !in_array($key, $this->getIdFields())) &&
                !in_array($key, $this->getExcludeProperties())
            ) {
                $insertValues[$this->translateDbName($key)] = $this->getObjectValue($value);
            }
        }

        return $insertValues;
    }
}
