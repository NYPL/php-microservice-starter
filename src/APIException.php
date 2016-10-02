<?php
namespace NYPL\API;

use Exception;

class APIException extends \Exception
{
    /**
     * @var array
     */
    public $debugInfo = [];

    /**
     * @param string $message
     * @param array|object $debugInfo
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = '', $debugInfo = [], $code = 0, Exception $previous = null)
    {
        if ($debugInfo) {
            $this->setDebugInfo($debugInfo);
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
}
