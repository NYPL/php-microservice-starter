<?php
namespace NYPL\Starter\Model\ModelTrait;

trait CacheTrait
{
    /**
     * @param int $id
     *
     * @return string
     */
    protected function getCacheKey($id = 0)
    {
        return $this->getObjectName() . ':' . $id;
    }
}
