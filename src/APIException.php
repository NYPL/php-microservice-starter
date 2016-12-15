<?php
namespace NYPL\Starter;

use Exception;
use NYPL\Starter\Model\Response\ErrorResponse;

class APIException extends \Exception
{
    /**
     * @var array
     */
    public $debugInfo = [];

    /**
     * @var int
     */
    public $httpCode = 500;

    /**
     * @var ErrorResponse
     */
    protected $errorResponse;

    /**
     * @param string $message
     * @param array|object $debugInfo
     * @param int $code
     * @param Exception $previous
     * @param int $httpCode
     * @param ErrorResponse $errorResponse
     */
    public function __construct(
        $message = '',
        $debugInfo = [],
        $code = 0,
        Exception $previous = null,
        $httpCode = 0,
        ErrorResponse $errorResponse = null
    ) {
        if ($debugInfo) {
            $this->setDebugInfo($debugInfo);
        }

        if ($httpCode) {
            $this->setHttpCode($httpCode);
        }

        if ($errorResponse) {
            $this->setErrorResponse($errorResponse);
        }

        parent::__construct($message, $code, $previous);
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

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = (int) $httpCode;
    }

    /**
     * @return ErrorResponse
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }

    /**
     * @param ErrorResponse $errorResponse
     */
    public function setErrorResponse(ErrorResponse $errorResponse)
    {
        $this->errorResponse = $errorResponse;
    }
}
