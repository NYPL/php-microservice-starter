<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Slim\DB;
use NYPL\Starter\Filter;

trait DBDeleteTrait
{
    use DBTrait;

    /**
     * @param Filter[] $filters
     *
     * @return bool
     * @throws APIException
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
