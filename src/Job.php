<?php
namespace NYPL\Starter;

use NYPL\Starter\JobStatus\JobStatusSuccess;

class Job
{
    protected $id = '';

    /**
     * @param string $id
     */
    public function __construct($id = '')
    {
        if ($id) {
            $this->setId($id);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param JobStatus $jobStatus
     *
     * @return bool
     */
    public function start(JobStatus $jobStatus)
    {
        return JobClient::startJob($this, $jobStatus);
    }

    /**
     * @param JobNotice $jobNotice
     *
     * @return bool
     */
    public function addNotice(JobNotice $jobNotice)
    {
        return JobClient::addNotice($this, $jobNotice);
    }

    /**
     * @param JobStatusSuccess $jobStatusSuccess
     *
     * @return bool
     */
    public function success(JobStatusSuccess $jobStatusSuccess)
    {
        return JobClient::success($this, $jobStatusSuccess);
    }

    /**
     * @param JobStatus $jobStatus
     *
     * @return bool
     */
    public function failure(JobStatus $jobStatus)
    {
        return JobClient::failure($this, $jobStatus);
    }
}
