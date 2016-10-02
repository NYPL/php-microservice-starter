<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseItem\Item;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="ItemsResponse", type="object")
 */
class ItemsResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Item[]
     */
    public $data;
}
