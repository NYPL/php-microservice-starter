<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BasePatron\Patron;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="ItemResponse", type="object")
 */
class PatronResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Patron
     */
    public $data;
}
