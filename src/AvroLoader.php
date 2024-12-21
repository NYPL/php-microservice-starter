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
            require __DIR__ . '/../lib/Avro/Avro.php';

            self::setLoaded(true);
        }
    }
}
