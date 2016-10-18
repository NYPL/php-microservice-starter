<?php
namespace NYPL\API;

use Monolog\Handler\SlackHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class APILogger
{
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

        //$log->pushHandler(new StreamHandler('error.log', Logger::DEBUG));

        $log->pushHandler(new SlackHandler(
            'xoxb-92411558631-OYXzR0omukZvqRzDv6wVV2Cv',
            'service-logging',
            'general-services',
            true,
            null,
            Logger::DEBUG
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
        if ($httpCode == 404) {
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
