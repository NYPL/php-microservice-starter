<?php
namespace NYPL\Starter;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;

class APILogger
{
    const DEFAULT_LOGGING_LEVEL = Logger::DEBUG;
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

        if (Config::isInitialized()) {
            $slackToken = Config::get('SLACK_TOKEN', null, true);

            if ($slackToken) {
                $handler = new SlackHandler(
                    Config::get('SLACK_TOKEN', null, true),
                    Config::get('SLACK_CHANNEL'),
                    Config::get('SLACK_USERNAME'),
                    true,
                    null,
                    Config::get('SLACK_LOGGING_LEVEL', self::DEFAULT_SLACK_LOGGING_LEVEL)
                );

                $log->pushHandler($handler);
            }
        }

        $handler = new ErrorLogHandler(
            ErrorLogHandler::OPERATING_SYSTEM,
            Config::get('DEFAULT_LOGGING_LEVEL', self::DEFAULT_LOGGING_LEVEL)
        );
        $handler->setFormatter(new JsonFormatter());

        $log->pushHandler($handler);

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
     * @param array|object $context
     *
     * @return bool
     */
    public static function addInfo($error = '', $context = [])
    {
        self::getLogger()->addInfo($error, (array) $context);

        return true;
    }

    /**
     * @param string $error
     * @param array|object $context
     *
     * @return bool
     */
    public static function addError($error = '', $context = [])
    {
        self::getLogger()->addError($error, (array) $context);

        return true;
    }

    /**
     * @param string $error
     * @param array $context
     *
     * @return bool
     */
    public static function addDebug($error = '', $context = [])
    {
        self::getLogger()->addDebug($error, (array) $context);

        return true;
    }

    /**
     * @param string $error
     * @param array $context
     *
     * @return bool
     */
    public static function addNotice($error = '', $context = [])
    {
        self::getLogger()->addNotice($error, (array) $context);

        return true;
    }
}
