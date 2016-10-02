<?php
namespace NYPL\API\Model\ModelTrait;

use NYPL\API\Model;
use NYPL\API\Model\ModelInterface\MessageInterface;
use NYPL\API\Model\LocalDateTime;

trait CreateTrait
{
    use DBTrait, MessageTrait;

    /**
     * @return string
     */
    abstract public function getIdName();

    /**
     * @return mixed
     */
    protected function getId()
    {
        $idGetter = "get{$this->getIdName()}";

        return $this->$idGetter();
    }

    /**
     * @param mixed $value
     */
    protected function setId($value)
    {
        $idSetter = "set{$this->getIdName()}";

        $this->$idSetter($value);
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

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return $value;
    }

    protected function checkCreatedDate()
    {
        $dateCreatedGetter = "getCreatedDate";
        $dateCreatedSetter = "setCreatedDate";

        if (method_exists($this, $dateCreatedGetter) && method_exists($this, $dateCreatedSetter)) {
            if (!$this->$dateCreatedGetter()) {
                $this->$dateCreatedSetter(new LocalDateTime(LocalDateTime::FORMAT_DATE_TIME_RFC));
            }
        }
    }

    /**
     * @param bool $useId
     *
     * @return string
     */
    public function create($useId = false)
    {
        if ($useId) {
            $this->checkExistingDb();
        }

        $this->checkCreatedDate();

        $this->createDbRecord($useId);

        if ($this instanceof MessageInterface) {
            $this->publishMessage($this->createMessage());
        }
    }
}
