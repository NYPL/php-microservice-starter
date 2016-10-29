<?php
namespace NYPL\ServiceSample\Model\DataModel;

use NYPL\Starter\Model;

/**
 * @SWG\Definition(type="object", required={"label"})
 */
class FixedField extends Model
{
    use Model\ModelTrait\TranslateTrait;

    /**
     * @SWG\Property(example="Language")
     * @var string
     */
    public $label;

    /**
     * @SWG\Property(example="eng")
     * @var string
     */
    public $value;

    /**
     * @SWG\Property(example="English")
     * @var string
     */
    public $display;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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

    /**
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @param string $display
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }
}
