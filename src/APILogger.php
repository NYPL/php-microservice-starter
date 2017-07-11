<?php
namespace NYPL\Starter;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\MissingExtensionException;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;
use NYPL\Starter\Formatter\NyplLogFormatter;

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
        $logger = new Logger('NYPL');

        self::addSlackLogging($logger);
        self::addJsonLogging($logger);

        self::setLogger($logger);
    }

    /**
     * @param Logger $logger
     * @throws APIException|MissingExtensionException
     */
    protected static function addSlackLogging(Logger $logger)
    {
        if (Config::isInitialized() && $slackToken = Config::get('SLACK_TOKEN', null, true)) {
            $handler = new SlackHandler(
                $slackToken,
                Config::get('SLACK_CHANNEL'),
                Config::get('SLACK_USERNAME'),
                true,
                null,
                Config::get('SLACK_LOGGING_LEVEL', self::DEFAULT_SLACK_LOGGING_LEVEL)
            );

            $logger->pushHandler($handler);
        }
    }

    /**
     * @param Logger $logger
     * @throws APIException|MissingExtensionException
     */
    protected static function addJsonLogging(Logger $logger)
    {
        $handler = new ErrorLogHandler(
            ErrorLogHandler::OPERATING_SYSTEM,
            Config::get('DEFAULT_LOGGING_LEVEL', self::DEFAULT_LOGGING_LEVEL)
        );
        $handler->setFormatter(new NyplLogFormatter());

        $logger->pushHandler($handler);
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

    protected static function formatContext($context)
    {
        if ($context instanceof \Throwable) {
            return [
                'file' => $context->getFile(),
                'line' => $context->getLine(),
                'trace' => $context->getTraceAsString(),
            ];
        }

        if (is_object($context)) {
            return (array) $context;
        }

        if (is_array($context)) {
            return $context;
        }
    }

    /**
     * @param string $error
     * @param array|object $context
     *
     * @return bool
     */
    public static function addInfo($error = '', $context = [])
    {
        self::getLogger()->addInfo($error, self::formatContext($context));

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
        self::getLogger()->addError($error, self::formatContext($context));

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
        self::getLogger()->addDebug($error, self::formatContext($context));

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
        self::getLogger()->addNotice($error, self::formatContext($context));

        return true;
    }
}
