<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseCheckout\Checkout;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="BibResponse", type="object")
 */
class CheckoutResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Checkout
     */
    public $data;
}
