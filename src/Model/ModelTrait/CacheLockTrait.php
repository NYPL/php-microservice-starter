<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Services\Model\CacheModel\JobStatus;
use NYPL\Starter\Cache;
use NYPL\Starter\Model;

trait CacheLockTrait
{
    /**
     * @param int $id
     *
     * @return string
     */
    protected function getLockKey($id = 0)
    {
        return $this->getCacheKey($id . ':lock');
    }

    /**
     * @param int $id
     * @param JobStatus|null $jobStatus
     *
     * @return bool
     */
    public function lock($id = 0, JobStatus $jobStatus = null)
    {
        if (Cache::getCache()->setnx($this->getLockKey($id), true)) {
            if ($jobStatus->getNotice()) {
                $this->addNotice($jobStatus->getNotice());
            }

            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @param JobStatus|null $jobStatus
     *
     * @return bool
     */
    public function unlock($id = 0, JobStatus $jobStatus = null)
    {
        if (Cache::getCache()->exists($this->getLockKey($id))) {
            if ($jobStatus->getNotice()) {
                $this->addNotice($jobStatus->getNotice());
            }

            Cache::getCache()->del($this->getLockKey($id));

            return true;
        }

        return false;
    }
}
