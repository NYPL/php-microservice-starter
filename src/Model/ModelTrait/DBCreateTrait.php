<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\DB;
use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;

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
     * @return string
     * @throws \Exception
     */
    public function create($useId = false)
    {
        if ($useId) {
            $this->checkExistingDb();
        }

        $this->checkCreatedDate();

        $insertId = $this->createDbRecord($useId);

        try {
            if ($this instanceof MessageInterface) {
                $this->publishMessage($this->getObjectName(), $this->createMessage());
            }
        } catch (\Exception $exception) {
            if ($this instanceof DeleteInterface) {
                $this->delete($insertId);
            }

            throw $exception;
        }

        return $insertId;
    }

    /**
     * @param bool $useId
     *
     * @return null|string
     */
    protected function createDbRecord($useId = false)
    {
        $insertValues = $this->getInsertValues($useId);

        $insertStatement = DB::getDatabase()->insert(array_keys($insertValues))
            ->into($this->getTableName())
            ->values(array_values($insertValues));

        $insertStatement->execute(true);

        if ($this->getSequenceId()) {
            $insertId = DB::getDatabase()->lastInsertId($this->getSequenceId());
        } else {
            $insertId = "";
        }

        if ($useId) {
            return $this->getId();
        }

        $this->setId($insertId);

        return $insertId;
    }
}
