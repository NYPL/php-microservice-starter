<?php
namespace NYPL\Starter\Model;

use NYPL\Starter\Config;

abstract class Response
{
    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $debugInfo = [];

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
     * @param string $type
     * @param mixed $debugInfo
     */
    public function addDebugInfo($type = '', $debugInfo = null)
    {
        if ($debugInfo) {
            $this->debugInfo[$type] = $debugInfo;
        }
    }
}
