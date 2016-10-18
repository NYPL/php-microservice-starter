<?php
namespace NYPL\Services\Model\Response\SuccessResponse;

use NYPL\Services\Model\DataModel\BaseBib\Bib;
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
