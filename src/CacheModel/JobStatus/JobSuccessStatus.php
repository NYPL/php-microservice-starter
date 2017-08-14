<?php
namespace NYPL\Starter\CacheModel\JobStatus;

use NYPL\Starter\CacheModel\JobStatus;

/**
 * @SWG\Definition(type="object")
 */
class JobSuccessStatus extends JobStatus
{
    /**
     * @SWG\Property(example="https://www.nypl.org/id/12121")
     * @var string
     */
    public $successRedirectUrl = '';

    /**
     * @return string
     */
    public function getSuccessRedirectUrl()
    {
        return $this->successRedirectUrl;
    }

    /**
     * @param string $successRedirectUrl
     */
    public function setSuccessRedirectUrl($successRedirectUrl)
    {
        $this->successRedirectUrl = $successRedirectUrl;
    }
}
