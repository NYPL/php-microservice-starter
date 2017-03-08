<?php
namespace NYPL\Starter;

use Monolog\Handler\SlackHandler;
use Monolog\Logger;

class APILogger
{
    const DEFAULT_SLACK_LOGGING_LEVEL = Logger::ERROR;

    /**
     * @var Logger
     */
    public static $logger;

    /**
     * @return Logger
     */
    public static function getLogger()
    {
        if (!self::$logger) {
            self::initializeLogger();
        }

        return self::$logger;
    }

    public static function initializeLogger()
    {
        $log = new Logger('API');

        $log->pushHandler(new SlackHandler(
            Config::get('SLACK_TOKEN'),
            Config::get('SLACK_CHANNEL'),
            Config::get('SLACK_USERNAME'),
            true,
            null,
            Config::get('SLACK_LOGGING_LEVEL', self::DEFAULT_SLACK_LOGGING_LEVEL)
        ));

        self::setLogger($log);
    }

    /**
     * @param Logger $logger
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    /**
     * @param int $httpCode
     * @param string $error
     * @param array $context
     *
     * @return bool
     */
    public static function addLog($httpCode = 0, $error = '', array $context = [])
    {
        if ($httpCode < 500) {
            return self::addInfo($error, $context);
        }

        return self::addError($error, $context);
    }

    /**
     * @param string $error
     * @param array $context
     *
     * @return bool
     */
    public static function addInfo($error = '', array $context = [])
    {
        self::getLogger()->addInfo($error, $context);

        return true;
    }

    /**
     * @param string $error
     * @param array $context
     *
     * @return bool
     */
    public static function addError($error = '', array $context = [])
    {
        self::getLogger()->addError($error, $context);

        return true;
    }
}
