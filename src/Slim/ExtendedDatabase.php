<?php
namespace NYPL\Starter\Slim;

use Slim\PDO\Database;

class ExtendedDatabase extends Database
{
    /**
     * @param array $columns
     *
     * @return ExtendedSelectStatement
     */
    public function select(array $columns = array('*'))
    {
        return new ExtendedSelectStatement($this, $columns);
    }
}
