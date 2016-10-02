<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseBib\Bib;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="BibsResponse", type="object")
 */
class BibsResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Bib[]
     */
    public $data;
}
