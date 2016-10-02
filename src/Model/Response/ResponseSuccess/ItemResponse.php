<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseItem\Item;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="ItemResponse", type="object")
 */
class ItemResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Item
     */
    public $data;
}
