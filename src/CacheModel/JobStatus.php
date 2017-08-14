<?php
namespace NYPL\Starter\CacheModel;

use NYPL\Starter\CacheModel;
use NYPL\Starter\CacheModel\JobNotice\JobNoticeCreated;

/**
 * @SWG\Definition(type="object")
 */
class JobStatus extends CacheModel
{
    /**
     * @SWG\Property
     * @var JobNoticeCreated
     */
    public $notice;

    /**
     * @SWG\Property(example="https://www.nypl.org/callback")
     * @var string
     */
    public $callBackUrl = '';

    /**
     * @SWG\Property(type="object")
     * @var array
     */
    public $callBackData = [];

    /**
     * @return JobNoticeCreated
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param JobNoticeCreated $notice
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    }

    /**
     * @param array|string $data
     *
     * @return JobNoticeCreated
     */
    public function translateNotice($data)
    {
        return new JobNoticeCreated($data, true);
    }

    /**
     * @return string
     */
    public function getCallBackUrl()
    {
        return $this->callBackUrl;
    }

    /**
     * @param string $callBackUrl
     */
    public function setCallBackUrl($callBackUrl)
    {
        $this->callBackUrl = $callBackUrl;
    }

    /**
     * @return array
     */
    public function getCallBackData()
    {
        return $this->callBackData;
    }

    /**
     * @param array $callBackData
     */
    public function setCallBackData($callBackData)
    {
        $this->callBackData = $callBackData;
    }
}
