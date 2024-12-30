<?php

namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;

interface ExtendedSelectOrUpdateInterface
{

    /**
     * ExtendedSelectStatement constructor.
     *
     * @param Database $dbh
     * @param array $columns
     */
    public function __construct(Database $dbh, array $columns);

    public function addParenthesis();

    public function closeParenthesis();

    public function getWhere();

}
