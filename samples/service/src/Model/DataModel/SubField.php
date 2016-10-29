<?php
namespace NYPL\ServiceSample\Model\DataModel;

use NYPL\Starter\Model;

/**
 * @SWG\Definition(type="object", required={"tag"})
 */
class SubField extends Model
{
    use Model\ModelTrait\TranslateTrait;

    /**
     * @SWG\Property(example="a")
     * @var string
     */
    public $tag;

    /**
     * @SWG\Property(example="Wizards")
     * @var string
     */
    public $content;

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
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
