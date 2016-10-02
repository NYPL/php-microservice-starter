<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\Schema;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="SchemasResponse", type="object")
 */
class SchemaResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Schema
     */
    public $data;
}
