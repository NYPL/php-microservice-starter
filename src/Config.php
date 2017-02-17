<?php
namespace NYPL\Starter;

use Dotenv\Dotenv;

class Config
{
    protected static $loaded = false;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function get($name = '')
    {
        if (!self::isLoaded()) {
            self::loadConfiguration();
        }

        return getenv($name);
    }

    protected static function loadConfiguration()
    {
        ini_set('display_errors', 'On');
        $dotEnv = new Dotenv(__DIR__ . '/../config/.env');
        $dotEnv->load();

        self::setLoaded(true);
    }

    /**
     * @return bool
     */
    public static function isLoaded(): bool
    {
        return self::$loaded;
    }

    /**
     * @param bool $loaded
     */
    public static function setLoaded(bool $loaded)
    {
        self::$loaded = $loaded;
    }
}
