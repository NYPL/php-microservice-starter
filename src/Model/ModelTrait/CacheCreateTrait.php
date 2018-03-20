<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\Cache;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;

trait CacheCreateTrait
{
    use CreateTrait;

    /**
     * @return string
     */
    abstract public function getIdKey();

    /**
     * @param bool $useId
     * @param int $expirationSeconds
     *
     * @return string
     * @throws \Exception
     */
    public function create($useId = false, $expirationSeconds = 0)
    {
        if (!$useId) {
            $count = Cache::getCache()->incr(
                $this->getIdKey()
            );

            $this->setId(
                uniqid($count)
            );
        }

        $cacheKey = $this->getCacheKey($this->getId());

        Cache::getCache()->hMset(
            $cacheKey,
            $this->getValueArray(true, get_object_vars($this))
        );

        if ($cacheKey && $expirationSeconds) {
            Cache::getCache()->setTimeout($cacheKey, $expirationSeconds);
        }

        try {
            if ($this instanceof MessageInterface) {
                $this->publishMessage($this->getStreamName(), $this->createMessage());
            }
        } catch (\Exception $exception) {
            if ($this instanceof DeleteInterface) {
                $this->delete($this->getCacheKey($this->getId()));
            }

            throw $exception;
        }
    }
}
