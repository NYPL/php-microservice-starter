<?php
namespace NYPL\API\Model\Response;

use NYPL\API\APIException;
use NYPL\API\Model\Response;

/**
 * @SWG\Definition(title="ErrorResponse", type="object")
 */
class ErrorResponse extends Response
{
    /**
     * @SWG\Property(format="int32")
     * @var int
     */
    public $statusCode;

    /**
     * @SWG\Property
     * @var string
     */
    public $type;

    /**
     * @SWG\Property
     * @var string
     */
    public $message;

    /**
     * @var array|object
     */
    public $debugInfo = [];

    /**
     * @var array
     */
    public $exception;

    /**
     * @param int $code
     * @param string $type
     * @param string $message
     * @param \Exception|APIException $exception
     */
    public function __construct($code, $type, $message, \Exception $exception)
    {
        $this->setStatusCode($code);

        $this->setType($type);

        $this->setMessage($message);

        if ($exception instanceof APIException) {
            $this->setDebugInfo($exception->getDebugInfo());
        }

        $this->initializeException($exception);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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

    /**
     * @return array
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param array $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @param \Exception $exception
     */
    public function initializeException(\Exception $exception)
    {
        $this->exception = [
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString()),
        ];
    }

    /**
     * @return array|object
     */
    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     * @param array|object $debugInfo
     */
    public function setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }
}
