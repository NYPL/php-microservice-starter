<?php
namespace NYPL\Starter;

use Exception;

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
     * @param string $message
     * @param array|object $debugInfo
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = '', $debugInfo = [], $code = 0, Exception $previous = null, $httpCode = 0)
    {
        if ($debugInfo) {
            $this->setDebugInfo($debugInfo);
        }

        if ($httpCode) {
            $this->setHttpCode($httpCode);
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
        $this->httpCode = $httpCode;
    }
}
