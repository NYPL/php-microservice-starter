<?php
namespace NYPL\Starter\Model;

/**
 * @SWG\Definition(name="BulkError", type="object")
 */
class BulkError
{
    /**
     * @SWG\Property(format="int32")
     * @var int
     */
    public $index = 0;

    /**
     * @SWG\Property(example="Description of error")
     * @var string
     */
    public $message = '';

    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $data = [];

    /**
     * @param int $index
     * @param string $message
     * @param array $data
     */
    public function __construct($index = 0, $message = '', array $data = [])
    {
        $this->setIndex($index);

        $this->setMessage($message);

        $this->setData($data);
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
