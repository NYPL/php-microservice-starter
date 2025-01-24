<?php
namespace NYPL\Starter\Slim;

class ExtendedWhereClause
{
    public function addParenthesis()
    {
        $this->container[] = '(';
    }

    public function closeParenthesis()
    {
        foreach ($this->container as $index => $container) {
            if ($container === '(') {
                // Remove parenthesis because it will be added back in subsequent statements
                $this->container[$index] = '';

                // For ANDs: Add the initial 'AND' and open parenthesis for the statement
                $this->container[$index + 1] = str_replace('AND ', 'AND (', $this->container[$index + 1]);

                if ($index === 0) {
                    // For ORs: For the first 'OR', remove the initial 'OR' and add the open parenthesis
                    $this->container[$index + 1] = str_replace('OR ', '(', $this->container[$index + 1]);
                } else {
                    // For ORs: Add open parenthesis for other 'OR' statements
                    $this->container[$index + 1] = str_replace('OR ', 'OR (', $this->container[$index + 1]);
                }
            }
        }

        $this->container[] = ')';
    }
}
