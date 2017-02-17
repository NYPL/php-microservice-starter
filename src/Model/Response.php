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

    protected function initializeDebug()
    {
        if (extension_loaded('xhprof') && Config::get('XH_PROF_BASE_URL')) {
            $profilerNamespace = 'Service';
            $xhprofData = xhprof_disable();
            $xhprofRuns = new \XHProfRuns_Default();
            $runID = $xhprofRuns->save_run($xhprofData, $profilerNamespace);

            $this->addDebugInfo(
                'performanceReport',
                sprintf(
                    Config::get('XH_PROF_BASE_URL') . '?run=%s&source=%s&sort=excl_wt',
                    $runID,
                    $profilerNamespace
                )
            );
        }
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
