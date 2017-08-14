<?php
namespace NYPL\Starter\CacheModel\BaseJob;

use NYPL\Starter\CacheModel\BaseJob;
use NYPL\Starter\CacheModel\JobNotice\JobNoticeCreated;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;
use NYPL\Starter\Model\ModelTrait\CacheCreateTrait;
use NYPL\Starter\Model\ModelTrait\CacheDeleteTrait;
use NYPL\Starter\Model\ModelTrait\CacheLockTrait;
use NYPL\Starter\Model\ModelTrait\CacheUpdateTrait;

/**
 * @SWG\Definition(title="Job", type="object", required={"id"})
 */
class Job extends BaseJob implements DeleteInterface
{
    use CacheCreateTrait, CacheUpdateTrait, CacheDeleteTrait, CacheLockTrait;

    /**
     * @SWG\Property(example="37580c4174e059a")
     * @var string
     */
    public $id = '';

    /**
     * @SWG\Property(example=false)
     * @var bool
     */
    public $started = false;

    /**
     * @SWG\Property(example=false)
     * @var bool
     */
    public $finished = false;

    /**
     * @SWG\Property(example=false)
     * @var bool
     */
    public $success = false;

    /**
     * @SWG\Property
     * @var JobNoticeCreated[]
     */
    public $notices;

    public function getSchema()
    {
        return
            [
                "name" => "Job",
                "type" => "record",
                "fields" => [
                    ["name" => "id", "type" => "string"],
                    ["name" => "started", "type" => "boolean"],
                    ["name" => "finished", "type" => "boolean"],
                    ["name" => "success", "type" => "boolean"],
                    ["name" => "notices" , "type" => [
                        "null",
                        ["type" => "array", "items" => [
                            ["name" => "JobNotice", "type" => "record", "fields" => [
                                ["name" => "text", "type" => ["string", "null"]]
                            ]]
                        ]],
                    ]],
                    ["name" => "successRedirectUrl", "type" => ["string", "null"]],
                    ["name" => "startCallbackUrl", "type" => ["string", "null"]],
                    ["name" => "failureCallbackUrl", "type" => ["string", "null"]],
                    ["name" => "updateCallbackUrl", "type" => ["string", "null"]]
                ]
            ];
    }

    public function getIdFields()
    {
        return ["id"];
    }

    public function getIdKey()
    {
        return "Jobs";
    }

    /**
     * @return boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @param boolean $started
     */
    public function setStarted($started)
    {
        $this->started = (bool) $started;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = (bool) $success;
    }

    /**
     * @return JobNoticeCreated[]
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * @param JobNoticeCreated[] $notices
     */
    public function setNotices($notices)
    {
        $this->notices = $notices;
    }

    /**
     * @param JobNoticeCreated $jobNotice
     */
    public function addNotice(JobNoticeCreated $jobNotice)
    {
        $jobNotice->setCreatedDate($jobNotice->translateCreatedDate());

        $this->notices[] = $jobNotice;
    }

    /**
     * @param array|string $data
     *
     * @return JobNoticeCreated[]
     */
    public function translateNotices($data)
    {
        if ($data) {
            return $this->translateArray($data, new JobNoticeCreated(), true);
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
     * @return boolean
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * @param boolean $finished
     */
    public function setFinished($finished)
    {
        $this->finished = (bool) $finished;
    }
}
