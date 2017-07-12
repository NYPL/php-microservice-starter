<?php
namespace NYPL\Starter\CacheModel;

use NYPL\Starter\CacheModel;

/**
 * @SWG\Definition(type="object")
 */
class JobNotice extends CacheModel
{
    /**
     * @SWG\Property(example="Processing has started...", type="string")
     * @var string
     */
    public $text = '';

    /**
     * @SWG\Property(type="object")
     * @var mixed
     */
    public $data;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
