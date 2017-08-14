<?php
namespace NYPL\Starter\Listener;

class ListenerResult implements \JsonSerializable
{
    public $processed = false;

    public $success = false;

    public $message = '';

    /**
     * @param bool $success
     * @param string $message
     */
    public function __construct($success, $message)
    {
        $this->setProcessed(true);

        $this->setSuccess($success);

        $this->setMessage($message);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return bool
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
