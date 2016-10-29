<?php
namespace NYPL\Starter;

use NYPL\Services\Config;

class Cache
{
    /**
     * @var \Redis
     */
    public static $cache;

    protected static function initializeCache()
    {
        $redis = new \Redis();
        $redis->connect(Config::CACHE_HOST, Config::CACHE_PORT);

        self::setCache($redis);
    }

    /**
     * @return \Redis
     */
    public static function getCache()
    {
        if (!self::$cache) {
            self::initializeCache();
        }

        return self::$cache;
    }

    /**
     * @param \Redis $cache
     */
    public static function setCache($cache)
    {
        self::$cache = $cache;
    }
}
