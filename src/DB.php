<?php
namespace NYPL\Starter;

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
            new Database(
                Config::get('DB_CONNECT_STRING'),
                Config::get('DB_USERNAME'),
                Config::get('DB_PASSWORD')
            )
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
