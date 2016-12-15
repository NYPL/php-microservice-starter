<?php
namespace NYPL\Starter\Filter;

use NYPL\Starter\Filter;

class QueryFilter extends Filter
{
    protected function getQueryParameterValue($queryParameterValue = null)
    {
        if ($queryParameterValue == 'null') {
            return null;
        }

        return $queryParameterValue;
    }

    public function __construct($queryParameterName = '', $queryParameterValue = null, $isJsonColumn = false)
    {
        parent::__construct(
            $queryParameterName,
            $this->getQueryParameterValue($queryParameterValue),
            $isJsonColumn
        );
    }
}
