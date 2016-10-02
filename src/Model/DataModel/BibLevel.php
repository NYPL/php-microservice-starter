<?php
namespace NYPL\API\Model\DataModel;

use NYPL\API\Model;
use NYPL\API\Model\ModelTrait\TranslateTrait;

/**
 * @SWG\Definition(type="object", required={"code"})
 */
class BibLevel extends Model
{
    use TranslateTrait;

    /**
     * @SWG\Property(example="m")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(example="MONOGRAPH")
     * @var string
     */
    public $value;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
