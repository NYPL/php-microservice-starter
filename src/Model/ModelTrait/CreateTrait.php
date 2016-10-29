<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\Model;
use NYPL\Starter\Model\LocalDateTime;

trait CreateTrait
{
    use MessageTrait;

    /**
     * @return string
     */
    abstract public function getIdName();

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

    protected function checkCreatedDate()
    {
        $dateCreatedGetter = 'getCreatedDate';
        $dateCreatedSetter = 'setCreatedDate';

        if (method_exists($this, $dateCreatedGetter) && method_exists($this, $dateCreatedSetter)) {
            if (!$this->$dateCreatedGetter()) {
                $this->$dateCreatedSetter(new LocalDateTime(LocalDateTime::FORMAT_DATE_TIME_RFC));
            }
        }
    }

    /**
     * @param bool $useId
     *
     * @return array
     */
    protected function getInsertValues($useId = false)
    {
        $insertValues = [];

        /**
         * @var Model $this
         */
        foreach (get_object_vars($this) as $key => $value) {
            if (($useId || $key !== $this->getIdName()) && !in_array($key, $this->getExcludeProperties())) {
                $insertValues[$this->translateDbName($key)] = $this->getObjectValue($value);
            }
        }

        return $insertValues;

    }
}
