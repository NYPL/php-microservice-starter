<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseHold\Hold;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="HoldResponse", type="object")
 */
class HoldResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Hold
     */
    public $data;
}
