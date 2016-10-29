<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;

trait DBTrait
{
    use TranslateTrait;

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->translateDbName($this->getObjectName());
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
}
