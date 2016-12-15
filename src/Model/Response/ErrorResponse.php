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
    public $statusCode = 500;

    /**
     * @SWG\Property(example="error_type")
     * @var string
     */
    public $type = '';

    /**
     * @SWG\Property(example="Description of error")
     * @var string
     */
    public $message = '';

    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $error = [];

    /**
     * @param int $statusCode
     * @param string $type
     * @param string $message
     * @param \Exception|\Throwable $exception
     */
    public function __construct($statusCode = 500, $type = '', $message = '', $exception = null)
    {
        $this->setStatusCode($statusCode);

        $this->setType($type);

        $this->setMessage($message);

        if ($exception) {
            $this->translateException($exception);
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
        if ($error instanceof \Exception) {
            $error = $this->translateException($error);
        }

        $this->error = $error;
    }

    /**
     * @param \Exception|\Throwable $error
     *
     * @return array
     */
    public function translateException($error)
    {
        $error = [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => explode("\n", $error->getTraceAsString()),
        ];

        if ($error instanceof APIException) {
            $this->addDebugInfo(
                'exception',
                $error->getDebugInfo()
            );
        }

        return $error;
    }
}
