<?php
namespace NYPL\Starter;

use NYPL\KafkaStarter\APIClient;
use NYPL\Starter\JobStatus\JobStatusSuccess;

class JobClient extends APIClient
{
    /**
     * @param Job $job
     * @param JobNotice $jobNotice
     *
     * @return bool
     * @throws APIException
     */
    public static function addNotice(Job $job, JobNotice $jobNotice)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            self::post(
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
    public static function startJob(Job $job, JobStatus $jobStatus)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            self::put(
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
    public static function success(Job $job, JobStatusSuccess $jobStatusSuccess)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            self::put(
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
    public static function failure(Job $job, JobStatus $jobStatus)
    {
        if (!$job->getId()) {
            return false;
        }

        try {
            self::put(
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
