<?php
namespace NYPL\ServiceSample\Model\Response\SuccessResponse;

use NYPL\ServiceSample\Model\DataModel\BaseBib\Bib;
use NYPL\Starter\Model\Response\SuccessResponse;

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
