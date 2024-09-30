<?php
namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;
use FaaPz\PDO\Statement\Select;
use FaaPz\PDO\Statement\SelectInterface;

class ExtendedDatabase extends Database
{
    /**
     * @param array $columns
     *
     * @return ExtendedSelectStatement
     */
    public function select(array $columns = ['*']): SelectInterface
    {
        return new ExtendedSelectStatement($this, $columns);
    }

}
