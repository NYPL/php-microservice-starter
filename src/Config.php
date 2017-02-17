<?php
namespace NYPL\Starter;

use Dotenv\Dotenv;

class Config
{
    const PUBLIC_CONFIG_FILE = '.public';
    const PRIVATE_CONFIG_FILE = '.private';

    protected static $initialized = false;

    protected static $configDirectory = '';

    protected static $required = [];

    /**
     * @param string $configDirectory
     * @param array $required
     */
    public static function initialize($configDirectory = '', array $required = [])
    {
        self::setConfigDirectory($configDirectory);

        if ($required) {
            self::setRequired($required);
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
        if (file_exists(self::getConfigDirectory() . '/' . self::PRIVATE_CONFIG_FILE)) {
            $dotEnv = new Dotenv(self::getConfigDirectory(), self::PRIVATE_CONFIG_FILE);
            $dotEnv->load();
        }

        $dotEnv = new Dotenv(self::getConfigDirectory(), self::PUBLIC_CONFIG_FILE);
        $dotEnv->load();

        $dotEnv->required('DB_USERNAME');
        $dotEnv->required('DB_PASSWORD');
        $dotEnv->required('SLACK_TOKEN');
        $dotEnv->required('AWS_ACCESS_KEY_ID');
        $dotEnv->required('AWS_SECRET_ACCESS_KEY');

        if (self::getRequired()) {
            foreach (self::getRequired() as $requiredName) {
                $dotEnv->required($requiredName);
            }
        }

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
    public static function getRequired()
    {
        return self::$required;
    }

    /**
     * @param array $required
     */
    public static function setRequired(array $required)
    {
        self::$required = $required;
    }
}
