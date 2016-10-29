<?php
namespace NYPL\ServiceSample\Model\Response\SuccessResponse;

use NYPL\ServiceSample\Model\DataModel\BaseBib\Bib;
use NYPL\Starter\Model\Response\SuccessResponse;

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
