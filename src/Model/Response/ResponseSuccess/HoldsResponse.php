<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseHold\Hold;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="HoldsResponse", type="object")
 */
class HoldsResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Hold[]
     */
    public $data;
}
