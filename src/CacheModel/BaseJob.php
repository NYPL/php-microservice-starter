<?php
namespace NYPL\Starter\CacheModel;

use NYPL\Starter\CacheModel;

class BaseJob extends CacheModel
{
    /**
     * @SWG\Property(example="https://www.nypl.org/item/121")
     * @var string
     */
    public $successRedirectUrl = '';

    /**
     * @SWG\Property(example="https://www.nypl.org/callback")
     * @var string
     */
    public $startCallbackUrl = '';

    /**
     * @SWG\Property(example="https://www.nypl.org/callback")
     * @var string
     */
    public $successCallbackUrl = '';

    /**
     * @SWG\Property(example="https://www.nypl.org/callback")
     * @var string
     */
    public $failureCallbackUrl = '';

    /**
     * @SWG\Property(example="https://www.nypl.org/callback")
     * @var string
     */
    public $updateCallbackUrl = '';

    /**
     * @param string $successRedirectUrl
     */
    public function setSuccessRedirectUrl($successRedirectUrl)
    {
        $this->successRedirectUrl = $successRedirectUrl;
    }

    /**
     * @return string
     */
    public function getSuccessRedirectUrl()
    {
        return $this->successRedirectUrl;
    }

    /**
     * @return string
     */
    public function getStartCallbackUrl()
    {
        return $this->startCallbackUrl;
    }

    /**
     * @param string $startCallbackUrl
     */
    public function setStartCallbackUrl($startCallbackUrl)
    {
        $this->startCallbackUrl = $startCallbackUrl;
    }

    /**
     * @return string
     */
    public function getSuccessCallbackUrl()
    {
        return $this->successCallbackUrl;
    }

    /**
     * @param string $successCallbackUrl
     */
    public function setSuccessCallbackUrl($successCallbackUrl)
    {
        $this->successCallbackUrl = $successCallbackUrl;
    }

    /**
     * @return string
     */
    public function getFailureCallbackUrl()
    {
        return $this->failureCallbackUrl;
    }

    /**
     * @param string $failureCallbackUrl
     */
    public function setFailureCallbackUrl($failureCallbackUrl)
    {
        $this->failureCallbackUrl = $failureCallbackUrl;
    }

    /**
     * @return string
     */
    public function getUpdateCallbackUrl()
    {
        return $this->updateCallbackUrl;
    }

    /**
     * @param string $updateCallbackUrl
     */
    public function setUpdateCallbackUrl($updateCallbackUrl)
    {
        $this->updateCallbackUrl = $updateCallbackUrl;
    }
}
