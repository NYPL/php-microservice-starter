<?php
namespace NYPL\Starter;

class Cache
{
    /**
     * @var \Redis
     */
    public static $cache;

    protected static function initializeCache()
    {
        $redis = new \Redis();
        $redis->connect(
            Config::get('CACHE_HOST'),
            Config::get('CACHE_PORT')
        );

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
