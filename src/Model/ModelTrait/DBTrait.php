<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\DB;
use NYPL\Starter\Filter;
use NYPL\Starter\Model;
use Slim\PDO\Statement\StatementContainer;

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
     * @return Filter[]
     */
    public function getIdFilters()
    {
        $filters = [];

        foreach ($this->getIdFields() as $idField) {
            $getterName = 'get' . $idField;

            $filters[] = new Filter($idField, $this->$getterName());
        }

        return $filters;
    }

    /**
     * @param Filter[] $filters
     * @param StatementContainer $sqlStatement
     */
    public function applyFilters(array $filters, StatementContainer $sqlStatement)
    {
        foreach ($filters as $filter) {
            $this->addWhere(
                $filter,
                $sqlStatement
            );
        }
    }

    /**
     * @return int
     */
    protected function checkExistingDb()
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->translateDbName($this->getTableName()));

        $this->applyFilters(
            $this->getIdFilters(),
            $selectStatement
        );

        $selectStatement = $selectStatement->execute();

        return $selectStatement->rowCount();
    }
}
