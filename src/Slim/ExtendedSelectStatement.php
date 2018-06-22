<?php
namespace NYPL\Starter\Slim;

use Slim\PDO\Database;
use Slim\PDO\Statement\SelectStatement;

class ExtendedSelectStatement extends SelectStatement
{
    /**
     * ExtendedSelectStatement constructor.
     *
     * @param Database $dbh
     * @param array $columns
     */
    public function __construct(Database $dbh, array $columns)
    {
        parent::__construct($dbh, $columns);

        $this->whereClause = new ExtendedWhereClause();
    }

    public function addParenthesis()
    {
        $this->whereClause->addParenthesis();
    }

    public function closeParenthesis()
    {
        $this->whereClause->closeParenthesis();
    }
}
