<?php
/**
 * Created by PhpStorm.
 * User: holingpoon
 * Date: 2/28/18
 * Time: 1:58 PM
 */

namespace NYPL\Starter;


class TotalCount
{
    /**
     * @var bool
     */
    public $includeCount = false;

    /**
     * @var int
     */
    public $count = 0;

    public function __construct($includeCount = false, $count = 0)
    {
        if ($includeCount) {
            $this->setIncludeCount($includeCount);
        }

        if ($count) {
            $this->setCount($count);
        }

    }

    /**
     * @return bool
     */
    public function isIncludeCount()
    {
        return $this->includeCount;
    }

    /**
     * @param bool $includeCount
     */
    public function setIncludeCount($includeCount)
    {
        $this->includeCount = $includeCount;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }
}
