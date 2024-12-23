<?php
namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;
use FaaPz\PDO\Statement\SelectInterface;
use FaaPz\PDO\Statement\UpdateInterface;

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

    /**
     * @param array $pairs
     *
     * @return UpdateInterface
     */
    public function update(array $pairs = []): UpdateInterface
    {
        return new ExtendedUpdateStatement($this, $pairs);
    }
}
