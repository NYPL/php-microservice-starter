<?php
namespace NYPL\API;

use NYPL\Services\Config;
use Slim\PDO\Database;

class DB
{
    /**
     * @var Database
     */
    public static $database;

    protected static function initializeDatabase()
    {
        self::setDatabase(
            new Database(Config::DB_CONNECT_STRING, Config::DB_USERNAME, Config::DB_PASSWORD)
        );
    }

    /**
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
