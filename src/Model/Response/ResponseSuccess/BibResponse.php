<?php
namespace NYPL\API\Model\Response\ResponseSuccess;

use NYPL\API\Model\DataModel\BaseBib\Bib;
use NYPL\API\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="BibResponse", type="object")
 */
class BibResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Bib
     */
    public $data;
}
