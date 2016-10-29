<?php
namespace NYPL\ServiceSample\Model\DataModel;

use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelTrait\TranslateTrait;

/**
 * @SWG\Definition(type="object", required={"code"})
 */
class Language extends Model
{
    use TranslateTrait;

    /**
     * @SWG\Property(example="eng")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(example="English")
     * @var string
     */
    public $name;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
