<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseCheckout\Checkout;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="BibsResponse", type="object")
 */
class CheckoutsResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Checkout[]
     */
    public $data;
}
