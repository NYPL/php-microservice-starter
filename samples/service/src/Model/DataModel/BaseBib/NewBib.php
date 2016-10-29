<?php
namespace NYPL\ServiceSample\Model\DataModel\BaseBib;

use NYPL\Starter\APIException;
use NYPL\ServiceSample\Model\DataModel\BaseBib;

/**
 * @SWG\Definition(type="object", required={"sourceCode"})
 */
class NewBib extends BaseBib
{
    /**
     * @SWG\Property(description="Indicates the source of the record (e.g. 's' for Sierra, 'r' for ReCAP')", example="s")
     * @var string
     */
    public $sourceCode;

    /**
     * @SWG\Property(description="Indicates the ID used by the source for this record (e.g. the 'BibId' in Sierra)", example=17746307)
     * @var int
     */
    public $sourceId;

    /**
     * @return string
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * @param $sourceCode
     *
     * @throws APIException
     */
    public function setSourceCode($sourceCode)
    {
        if (!$sourceCode) {
            throw new APIException('SourceCode is required');
        }

        $this->sourceCode = $sourceCode;
    }

    /**
     * @return int
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param int $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = (int) $sourceId;
    }
}
