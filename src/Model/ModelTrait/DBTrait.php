<?php
namespace NYPL\API\Model\ModelTrait;

use NYPL\API\APIException;
use NYPL\API\DB;

trait DBTrait
{
    /**
     * @return string
     */
    public function getTableName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }

    /**
     * @throws APIException
     */
    protected function checkExistingDb()
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->getTableName())
            ->where($this->getIdName(), '=', $this->getId());

        $selectStatement = $selectStatement->execute();

        if ($selectStatement->rowCount()) {
            throw new APIException('ID specified (' . $this->getId() . ') already exists');
        }
    }

    /**
     * @param bool $useId
     *
     * @return null|string
     */
    protected function createDbRecord($useId = false)
    {
        $insertValues = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (($useId || $key !== $this->getIdName()) && !in_array($key, $this->getExcludeProperties())) {
                $insertValues[$key] = $this->getObjectValue($value);
            }
        }

        $insertStatement = DB::getDatabase()->insert(array_keys($insertValues))
            ->into($this->getTableName())
            ->values(array_values($insertValues));

        $insertId = $insertStatement->execute(true);

        if ($useId) {
            return $this->getId();
        }

        $this->setId($insertId);

        return $insertId;
    }
}
