<?php
namespace NYPL\Starter;

use NYPL\Starter\CacheModel\BaseJob\Job;
use NYPL\Starter\CacheModel\JobNotice;
use NYPL\Starter\CacheModel\JobStatus;
use NYPL\Starter\JobStatus\JobStatusSuccess;

class JobClient extends APIClient
{
    /**
     * @return Job
     */
    public static function createNewJob()
    {
        $job = new Job();

        $jobClient = new JobClient();

        return $jobClient->createJob($job);
    }

    /**
     * @return bool
     */
    protected function isRequiresAuth()
    {
        return false;
    }

    /**
     * @param Job $job
     *
     * @return Job
     * @throws APIException
     */
    public function createJob(Job $job)
    {
        try {
            $response = $this->post(
                'jobs',
                [
                    'body' => json_encode($job),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            $body = json_decode($response->getBody(), true);

            $job->setId($body['data']['id']);

            return $job;
        } catch (\Exception $exception) {
            throw new APIException($exception->getMessage());
        }
    }

    /**
     * @param Job $job
     * @param JobNotice $jobNotice
     *
     * @return bool
     * @throws APIException
     */
    public function addNotice(Job $job, JobNotice $jobNotice)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            $this->post(
                'jobs/' . $job->getId() . '/notices',
                [
                    'body' => json_encode($jobNotice),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            APILogger::addNotice(
                $jobNotice->getText(),
                $jobNotice->getData()
            );
        } catch (\Exception $exception) {
            throw new APIException($exception->getMessage());
        }
    }

    /**
     * @param Job $job
     * @param JobStatus $jobStatus
     *
     * @return bool
     * @throws APIException
     */
    public function startJob(Job $job, JobStatus $jobStatus)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            $this->put(
                'jobs/' . $job->getId() . '/start',
                [
                    'body' => json_encode($jobStatus),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            APILogger::addNotice(
                $jobStatus->getNotice()->getText(),
                $jobStatus->getNotice()->getData()
            );
        } catch (\Exception $exception) {
            throw new APIException($exception->getMessage());
        }
    }

    /**
     * @param Job $job
     * @param JobStatusSuccess $jobStatusSuccess
     *
     * @return bool
     * @throws APIException
     */
    public function success(Job $job, JobStatusSuccess $jobStatusSuccess)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            $this->put(
                'jobs/' . $job->getId() . '/success',
                [
                    'body' => json_encode($jobStatusSuccess),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            APILogger::addInfo(
                $jobStatusSuccess->getNotice()->getText(),
                $jobStatusSuccess->getNotice()->getData()
            );
        } catch (\Exception $exception) {
            throw new APIException($exception->getMessage());
        }
    }

    /**
     * @param Job $job
     * @param JobStatus $jobStatus
     *
     * @return bool
     * @throws APIException
     */
    public function failure(Job $job, JobStatus $jobStatus)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            $this->put(
                'jobs/' . $job->getId() . '/failure',
                [
                    'body' => json_encode($jobStatus),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            APILogger::addError(
                $jobStatus->getNotice()->getText(),
                $jobStatus->getNotice()->getData()
            );
        } catch (\Exception $exception) {
            throw new APIException($exception->getMessage());
        }
    }
}
