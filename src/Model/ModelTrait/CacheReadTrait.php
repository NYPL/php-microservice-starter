<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Cache;

trait CacheReadTrait
{
    public function read($id = "")
    {
        $values = Cache::getCache()->hGetAll(
            $this->getCacheKey($id)
        );

        if (!$values) {
            throw new APIException("No record found", [], 0, null, 404);
        }

        $this->translate($values);

        return true;
    }
}
