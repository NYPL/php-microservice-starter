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
            $this->getInsertValues(true)
        );

        if ($this instanceof MessageInterface) {
            $this->publishMessage($this->getObjectName(), $this->createMessage());
        }
    }
}
