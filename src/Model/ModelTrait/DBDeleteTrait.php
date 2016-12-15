<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\DB;
use NYPL\Starter\Filter;
use NYPL\Starter\Model;

trait DBDeleteTrait
{
    /**
     * @param Filter[] $filters
     *
     * @return bool
     */
    public function delete(array $filters = [])
    {
        $sqlStatement = DB::getDatabase()->delete()
            ->from($this->getTableName());

        $this->applyFilters($filters, $sqlStatement);

        $sqlStatement->execute();

        return true;
    }
}
