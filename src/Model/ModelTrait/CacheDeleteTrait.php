<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\Cache;
use NYPL\Starter\Model;

trait CacheDeleteTrait
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function delete($id = '')
    {
        Cache::getCache()->del($this->getCacheKey($id));

        return true;
    }
}
