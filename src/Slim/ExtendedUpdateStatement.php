<?php
namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;
use FaaPz\PDO\Statement\Select;
use FaaPz\PDO\Statement\Update;
use GuzzleHttp\Client;

class ExtendedUpdateStatement extends Update
{
    /**
     * @var ExtendedWhereClause
     */
    protected $whereClause;

    /**
     * ExtendedUpdateStatement constructor.
     * @param Database                                                     $dbh
     * @param array<string, float|int|string|RawInterface|SelectInterface> $pairs
     */
    public function __construct(Database $dbh, array $pairs = [])
    {
        parent::__construct($dbh, $pairs);

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

    public function getWhere() {
        return $this->where;
    }

}
