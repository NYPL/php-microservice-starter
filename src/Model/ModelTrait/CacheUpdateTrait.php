<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\Cache;
use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelInterface\MessageInterface;

trait CacheUpdateTrait
{
    /**
     * @return string
     * @throws \Exception
     */
    public function update()
    {
        Cache::getCache()->hMset(
            $this->getCacheKey($this->getId()),
            $this->getValueArray(true, get_object_vars($this))
        );

        if ($this instanceof MessageInterface) {
            $this->publishMessage($this->getStreamName(), $this->createMessage());
        }
    }
}
