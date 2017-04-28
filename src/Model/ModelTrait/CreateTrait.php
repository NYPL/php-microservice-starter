<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Model\LocalDateTime;

trait CreateTrait
{
    use MessageTrait;

    /**
     * @return array
     */
    abstract public function getIdFields();

    /**
     * @param bool $useId
     *
     * @return string
     * @throws \Exception
     */
    abstract public function create($useId = false);

    /**
     * @return string
     */
    public function getObjectName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function getObjectValue($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format("Y-m-d H:i:s");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE_TIME) {
            return $value->getDateTime()->format("Y-m-d H:i:s");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE) {
            return $value->getDateTime()->format("Y-m-d");
        }

        if ($value instanceof LocalDateTime && $value->getFormat() == LocalDateTime::FORMAT_DATE_TIME_RFC) {
            return $value->getDateTime()->format("c");
        }

        if (is_bool($value)) {
            return (int) $value;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return $value;
    }

    /**
     * @return string
     * @throws APIException
     */
    public function getFullId()
    {
        $idValues = [];

        foreach ($this->getIdFields() as $idField) {
            $getterName = 'get' . $idField;

            if (!method_exists($this, $getterName)) {
                throw new APIException('Getter for ID field does not exist');
            }

            $idValues[] = $this->$getterName();
        }

        return implode(':', $idValues);
    }
}
