<?php
namespace NYPL\Starter;

class JobNotice implements \JsonSerializable
{
    protected $text = '';

    protected $data;

    /**
     * @param string $text
     * @param mixed $data
     */
    public function __construct($text = '', $data = null)
    {
        if ($text) {
            $this->setText($text);
        }

        if ($data) {
            $this->setData($data);
        }
    }

    public function jsonSerialize()
    {
        $jsonArray = [];

        foreach (get_object_vars($this) as $objectName => $objectValue) {
            $jsonArray[$objectName] = $objectValue;
        }

        return $jsonArray;
    }

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
