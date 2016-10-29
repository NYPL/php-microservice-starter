<?php
namespace NYPL\Starter\Model\Response;

use NYPL\Starter\APIException;
use NYPL\Starter\Model\Response;

/**
 * @SWG\Definition(name="ErrorResponse", type="object")
 */
class ErrorResponse extends Response
{
    /**
     * @SWG\Property(format="int32")
     * @var int
     */
    public $statusCode;

    /**
     * @SWG\Property(example="error_type")
     * @var string
     */
    public $type;

    /**
     * @SWG\Property(example="Description of error")
     * @var string
     */
    public $message;

    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $error;

    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $debugInfo = [];

    /**
     * @param int $code
     * @param string $type
     * @param string $message
     * @param \Exception|\Throwable $throwable
     */
    public function __construct($code, $type, $message, $throwable = null)
    {
        $this->setStatusCode($code);

        $this->setType($type);

        $this->setMessage($message);

        if ($throwable) {
            $this->initializeError($throwable);

            if ($throwable instanceof APIException) {
                $this->setDebugInfo($throwable->getDebugInfo());
            }
        }
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
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param array $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param \Exception|\Throwable $error
     */
    public function initializeError($error)
    {
        $this->error = [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => explode("\n", $error->getTraceAsString()),
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
