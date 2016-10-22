<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use Stringy\Stringy;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;

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
            ->from($this->translateDbName($this->getTableName()))
            ->where($this->translateDbName($this->getIdName()), '=', $this->getId());

        $selectStatement = $selectStatement->execute();

        if ($selectStatement->rowCount()) {
            if ($this instanceof DeleteInterface) {
                $this->delete($this->getId());
            }
        }
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
     * @param bool $useId
     *
     * @return null|string
     */
    protected function createDbRecord($useId = false)
    {
        $insertValues = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (($useId || $key !== $this->getIdName()) && !in_array($key, $this->getExcludeProperties())) {
                $insertValues[$this->translateDbName($key)] = $this->getObjectValue($value);
            }
        }

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
