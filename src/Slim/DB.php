<?php
namespace NYPL\Starter\Slim;

use NYPL\Starter\APIException;
use NYPL\Starter\Config;

class DB
{
    /**
     * @var ExtendedDatabase
     */
    public static $database;

    /**
     * @throws APIException
     */
    protected static function initializeDatabase()
    {
        self::setDatabase(
            new ExtendedDatabase(
                Config::get('DB_CONNECT_STRING'),
                Config::get('DB_USERNAME'),
                Config::get('DB_PASSWORD', null, true)
            )
        );
    }

    /**
     * @throws APIException
     * @return ExtendedDatabase
     */
    public static function getDatabase()
    {
        if (!self::$database) {
            self::initializeDatabase();
        }

        return self::$database;
    }

    /**
     * @param ExtendedDatabase $database
     */
    public static function setDatabase($database)
    {
        self::$database = $database;
    }
}
