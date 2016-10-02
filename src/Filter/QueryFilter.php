<?php
namespace NYPL\API\Filter;

use NYPL\API\Filter;
use Slim\Http\Request;

class QueryFilter extends Filter
{
    public function __construct(Request $request, $queryParameterName = '', $isJsonColumn = false)
    {
        if ($filterValue = $request->getQueryParam($queryParameterName)) {
            parent::__construct(
                $queryParameterName,
                $filterValue,
                $isJsonColumn
            );
        }
    }
}
