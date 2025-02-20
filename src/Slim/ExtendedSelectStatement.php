<?php
namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;
use FaaPz\PDO\Statement\Select;

class ExtendedSelectStatement extends Select implements ExtendedSelectOrUpdateInterface
{
    /**
     * @var ExtendedWhereClause
     */
    protected $whereClause;

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

    public function getWhere()
    {
        return $this->where;
    }
}
