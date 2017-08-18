<?php
namespace NYPL\Starter;

class AppCache
{
    protected static $cacheDir = 'nypl_app_cache';

    /**
     * @var array
     */
    protected static $memoryCache = [];

    /**
     * @var bool
     */
    protected static $initialized = false;

    /**
     * @param string $key
     * @param null $data
     * @param int $ttl
     */
    public static function set($key = '', $data = null, $ttl = 0)
    {
        self::initialize();

        self::$memoryCache[$key] = $data;

        FileSystemCache::store(
            FileSystemCache::generateCacheKey($key),
            $data,
            (int) $ttl
        );
    }

    public static function get($key = '')
    {
        self::initialize();

        if (Config::isLocalEnvironment()) {
            return false;
        }

        if (isset(self::$memoryCache[$key])) {
            return self::$memoryCache[$key];
        }

        return FileSystemCache::retrieve(
            FileSystemCache::generateCacheKey($key)
        );
    }

    protected static function initialize()
    {
        if (!self::isInitialized()) {
            FileSystemCache::$cacheDir = sys_get_temp_dir() . '/' . self::$cacheDir;

            self::setInitialized(true);
        }
    }

    /**
     * @return bool
     */
    public static function isInitialized()
    {
        return self::$initialized;
    }

    /**
     * @param bool $initialized
     */
    public static function setInitialized($initialized)
    {
        self::$initialized = (bool) $initialized;
    }
}
