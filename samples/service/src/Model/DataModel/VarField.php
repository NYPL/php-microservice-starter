<?php
namespace NYPL\ServiceSample\Model\DataModel;

use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelTrait\TranslateTrait;

/**
 * @SWG\Definition(type="object", required={"fieldTag"})
 */
class VarField extends Model
{
    use TranslateTrait;

    /**
     * @SWG\Property(example="a")
     * @var string
     */
    public $fieldTag;

    /**
     * @SWG\Property(example="100")
     * @var string
     */
    public $marcTag;

    /**
     * @SWG\Property(example="1")
     * @var string
     */
    public $ind1;

    /**
     * @SWG\Property()
     * @var string
     */
    public $ind2;

    /**
     * @SWG\Property()
     * @var string
     */
    public $content;

    /**
     * @SWG\Property()
     * @var SubField[]
     */
    public $subFields;

    /**
     * @return string
     */
    public function getFieldTag()
    {
        return $this->fieldTag;
    }

    /**
     * @param string $fieldTag
     */
    public function setFieldTag($fieldTag)
    {
        $this->fieldTag = $fieldTag;
    }

    /**
     * @return string
     */
    public function getMarcTag()
    {
        return $this->marcTag;
    }

    /**
     * @param string $marcTag
     */
    public function setMarcTag($marcTag)
    {
        $this->marcTag = $marcTag;
    }

    /**
     * @return string
     */
    public function getInd1()
    {
        return $this->ind1;
    }

    /**
     * @param string $ind1
     */
    public function setInd1($ind1)
    {
        $this->ind1 = $ind1;
    }

    /**
     * @return string
     */
    public function getInd2()
    {
        return $this->ind2;
    }

    /**
     * @param string $ind2
     */
    public function setInd2($ind2)
    {
        $this->ind2 = $ind2;
    }

    /**
     * @return SubField[]
     */
    public function getSubFields()
    {
        return $this->subFields;
    }

    /**
     * @param SubField[] $subFields
     */
    public function setSubFields($subFields)
    {
        $this->subFields = $subFields;
    }

    /**
     * @param array $data
     *
     * @return SubField
     */
    public function translateSubFields(array $data = [])
    {
        return $this->translateArray($data, new SubField());
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
