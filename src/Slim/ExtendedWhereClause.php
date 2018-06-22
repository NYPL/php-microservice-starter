<?php
namespace NYPL\Starter\Slim;

use Slim\PDO\Clause\WhereClause;

class ExtendedWhereClause extends WhereClause
{
    public function addParenthesis()
    {
        $this->container[] = '(';
    }

    public function closeParenthesis()
    {
        foreach ($this->container as $index => $container) {
            if ($container === '(') {
                $this->container[$index] = '';

                $this->container[$index + 1] = str_replace('AND ', 'AND (', $this->container[$index + 1]);
            }
        }

        $this->container[] = ')';
    }
}
