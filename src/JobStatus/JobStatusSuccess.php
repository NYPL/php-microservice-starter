<?php
namespace NYPL\Starter\JobStatus;

use NYPL\Starter\CacheModel\JobStatus;

class JobStatusSuccess extends JobStatus
{
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
