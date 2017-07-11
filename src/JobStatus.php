<?php
namespace NYPL\Starter;

class JobStatus implements \JsonSerializable
{
    /**
     * @var JobNotice
     */
    public $notice;

    /**
     * @var string
     */
    public $callbackUrl = '';

    /**
     * @var array
     */
    public $callbackData = [];

    /**
     * @param string $notice
     * @param mixed $data
     */
    public function __construct($notice = '', $data = null)
    {
        if ($notice) {
            $this->setNotice(new JobNotice($notice, $data));
        }
    }

    public function jsonSerialize()
    {
        $jsonArray = [];

        foreach (get_object_vars($this) as $objectName => $objectValue) {
            $jsonArray[$objectName] = $objectValue;
        }

        return $jsonArray;
    }

    /**
     * @return JobNotice
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param JobNotice $notice
     */
    public function setNotice(JobNotice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return array
     */
    public function getCallbackData()
    {
        return $this->callbackData;
    }

    /**
     * @param array $callbackData
     */
    public function setCallbackData($callbackData)
    {
        $this->callbackData = $callbackData;
    }
}
