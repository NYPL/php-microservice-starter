<?php
namespace NYPL\Starter\Slim;

use FaaPz\PDO\Database;
use NYPL\Starter\APIException;
use NYPL\Starter\Config;

class DB
{
    /**
     * @var Database
     */
    public static $database;

    /**
     * @throws APIException
     */
    protected static function initializeDatabase()
    {
        self::setDatabase(
            new Database(
                Config::get('DB_CONNECT_STRING'),
                Config::get('DB_USERNAME'),
                Config::get('DB_PASSWORD', null, true)
            )
        );
    }

    /**
     * @throws APIException
     * @return Database
     */
    public static function getDatabase()
    {
        if (!self::$database) {
            self::initializeDatabase();
        }

        return self::$database;
    }

    /**
     * @param Database $database
     */
    public static function setDatabase($database)
    {
        self::$database = $database;
    }
}
