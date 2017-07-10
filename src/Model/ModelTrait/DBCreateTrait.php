<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;
use NYPL\Starter\Model\LocalDateTime;

trait DBCreateTrait
{
    use DBTrait, CreateTrait;

    /**
     * @return string
     */
    abstract public function getSequenceId();

    /**
     * @param bool $useId
     *
     * @return bool|null|string
     * @throws APIException
     * @throws \Exception
     */
    public function create($useId = false)
    {
        if (!$this->getRawData()) {
            throw new APIException('No data provided for request');
        }

        if ($useId) {
            if ($this->checkExistingDb()) {
                $this->setFilters($this->getIdFilters());

                $this->update($this->getRawData());

                return true;
            }
        }

        $this->checkCreatedDate();

        $insertId = $this->createDbRecord($useId);

        try {
            if ($this instanceof MessageInterface && !$this->isBulk()) {
                $this->publishMessage($this->getStreamName(), $this->createMessage());
            }
        } catch (\Exception $exception) {
            if ($this instanceof DeleteInterface) {
                $this->delete($this->getIdFilters());
            }

            throw $exception;
        }

        return $insertId;
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
     * @return null|string
     */
    protected function createDbRecord($useId = false)
    {
        $insertValues = $this->getValueArray($useId, get_object_vars($this));

        $insertStatement = DB::getDatabase()->insert(array_keys($insertValues))
            ->into($this->getTableName())
            ->values(array_values($insertValues));

        $insertStatement->execute(false);

        if ($this->getSequenceId()) {
            $insertId = DB::getDatabase()->lastInsertId($this->getSequenceId());
        } else {
            $insertId = "";
        }

        if ($useId) {
            return $this->getFullId();
        }

        $this->setId($insertId);

        return $insertId;
    }
}
