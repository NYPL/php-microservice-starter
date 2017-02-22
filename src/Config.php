<?php
namespace NYPL\Starter;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class Config
{
    const PUBLIC_CONFIG_FILE = '.public';
    const PRIVATE_CONFIG_FILE = '.private';

    protected static $initialized = false;

    protected static $configDirectory = '';

    protected static $publicRequired =
        [
            'TIME_ZONE', 'DB_CONNECT_STRING', 'SLACK_CHANNEL', 'SLACK_USERNAME', 'AWS_DEFAULT_REGION'
        ];

    protected static $privateRequired =
        [
            'SLACK_TOKEN', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY'
        ];

    protected static $addedRequired = [];

    /**
     * @param string $configDirectory
     * @param array $required
     */
    public static function initialize($configDirectory = '', array $required = [])
    {
        self::setConfigDirectory($configDirectory);

        if ($required) {
            self::setAddedRequired($required);
        }

        self::loadConfiguration();

        self::setInitialized(true);
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws APIException
     */
    public static function get($name = '')
    {
        if (!self::isInitialized()) {
            throw new APIException('Configuration has not been initialized');
        }

        return getenv($name);
    }

    protected static function loadConfiguration()
    {
        try {
            $dotEnv = new Dotenv(self::getConfigDirectory(), self::PRIVATE_CONFIG_FILE);
            $dotEnv->load();
        } catch (InvalidPathException $exception) {
        }

        $dotEnv->required(self::getPrivateRequired());

        $dotEnv = new Dotenv(self::getConfigDirectory(), self::PUBLIC_CONFIG_FILE);
        $dotEnv->load();

        $dotEnv->required(self::getPublicRequired());

        $dotEnv->required(self::getAddedRequired());

        self::setInitialized(true);
    }

    /**
     * @return bool
     */
    protected static function isInitialized(): bool
    {
        return self::$initialized;
    }

    /**
     * @param bool $initialized
     */
    protected static function setInitialized(bool $initialized)
    {
        self::$initialized = $initialized;
    }

    /**
     * @return string
     */
    protected static function getConfigDirectory(): string
    {
        return self::$configDirectory;
    }

    /**
     * @param string $configDirectory
     */
    protected static function setConfigDirectory(string $configDirectory)
    {
        self::$configDirectory = $configDirectory;
    }

    /**
     * @return array
     */
    public static function getAddedRequired()
    {
        return self::$addedRequired;
    }

    /**
     * @param array $addedRequired
     */
    public static function setAddedRequired(array $addedRequired)
    {
        self::$addedRequired = $addedRequired;
    }

    /**
     * @return array
     */
    public static function getPublicRequired()
    {
        return self::$publicRequired;
    }

    /**
     * @return array
     */
    public static function getPrivateRequired(): array
    {
        return self::$privateRequired;
    }
}
