<?php
namespace NYPL\Starter;

class AvroLoader
{
    /**
     * @var bool
     */
    public static $loaded;

    /**
     * @return bool
     */
    public static function isLoaded()
    {
        return self::$loaded;
    }

    /**
     * @param bool $loaded
     */
    public static function setLoaded($loaded)
    {
        self::$loaded = (bool) $loaded;
    }

    public static function load()
    {
        if (!self::isLoaded()) {
            require __DIR__ . '/../avro-php-1.8.1/lib/avro.php';

            self::setLoaded(true);
        }
    }
}
