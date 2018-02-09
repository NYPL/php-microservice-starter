<?php
namespace NYPL\Starter;

use Aws\Kms\KmsClient;
use Dotenv\Dotenv;

class Config
{
    protected const LOCAL_ENVIRONMENT_FILE = 'local.env';
    protected const GLOBAL_ENVIRONMENT_FILE = 'global.env';
    protected const DEFAULT_TIME_ZONE = 'America/New_York';
    protected const CACHE_PREFIX = 'Config:';

    protected static $initialized = false;

    protected static $configDirectory = '';

    /**
     * @var KmsClient
     */
    protected static $keyClient;

    /**
     * @param string $configDirectory
     * @throws APIException|\InvalidArgumentException
     */
    public static function initialize($configDirectory = '')
    {
        self::setConfigDirectory($configDirectory);

        self::loadConfiguration();

        self::setInitialized(true);

        date_default_timezone_set(
            self::get('TIME_ZONE', self::DEFAULT_TIME_ZONE)
        );
    }

    /**
     * @param string $name
     * @param null $defaultValue
     * @param bool $isEncrypted
     *
     * @throws APIException|\InvalidArgumentException
     * @return null|string
     */
    public static function get($name = '', $defaultValue = null, $isEncrypted = false)
    {
        if (getenv($name) !== false) {
            if ($isEncrypted && self::isEncryptedEnvironment()) {
                return self::decryptEnvironmentVariable($name);
            }

            return (string) getenv($name);
        }

        return $defaultValue;
    }

    /**
     * @throws APIException
     * @return bool
     */
    public static function isLocalEnvironment()
    {
        if (self::get('LAMBDA_TASK_ROOT')) {
            return false;
        }

        return true;
    }

    /**
     * @throws APIException
     * @return bool
     */
    protected static function isEncryptedEnvironment()
    {
        if (self::get('LAMBDA_TASK_ROOT')) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @throws APIException|\InvalidArgumentException
     * @return string
     */
    protected static function decryptEnvironmentVariable($name = '')
    {
        if (!getenv($name)) {
            return '';
        }

        $cacheKey = self::CACHE_PREFIX . $name;

        if ($decryptedValue = AppCache::get($cacheKey)) {
            return $decryptedValue;
        }

        $decryptedValue = (string) self::getKeyClient()->decrypt([
            'CiphertextBlob' => base64_decode(getenv($name)),
        ])['Plaintext'];

        AppCache::set($cacheKey, $decryptedValue);

        return $decryptedValue;
    }

    /**
     * @throws APIException
     */
    protected static function loadLocalEnvironment()
    {
        $localEnvironmentFile = self::getConfigDirectory() . '/' . self::LOCAL_ENVIRONMENT_FILE;

        if (!file_exists($localEnvironmentFile)) {
            throw new APIException(
                'Unable to load local environment configuration file (' . $localEnvironmentFile . ')'
            );
        }

        $dotEnv = new Dotenv(self::getConfigDirectory(), self::LOCAL_ENVIRONMENT_FILE);
        $dotEnv->load();
    }

    /**
     * @throws APIException
     */
    protected static function loadConfiguration()
    {
        if (self::isLocalEnvironment()) {
            self::loadLocalEnvironment();
        }

        if (file_exists(self::getConfigDirectory() . '/' . self::GLOBAL_ENVIRONMENT_FILE)) {
            $dotEnv = new Dotenv(self::getConfigDirectory(), self::GLOBAL_ENVIRONMENT_FILE);
            $dotEnv->load();
        }

        self::setInitialized(true);
    }

    /**
     * @return bool
     */
    public static function isInitialized()
    {
        return self::$initialized;
    }

    /**
     * @param bool $initialized
     */
    protected static function setInitialized($initialized)
    {
        self::$initialized = $initialized;
    }

    /**
     * @return string
     */
    protected static function getConfigDirectory()
    {
        return self::$configDirectory;
    }

    /**
     * @param string $configDirectory
     */
    protected static function setConfigDirectory($configDirectory = '')
    {
        self::$configDirectory = $configDirectory;
    }

    /**
     * @throws \InvalidArgumentException|APIException
     * @return KmsClient
     */
    protected static function createKeyClient()
    {
        return new KmsClient([
            'version' => 'latest',
            'region'  => self::get('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => self::get('AWS_ACCESS_KEY_ID'),
                'secret' => self::get('AWS_SECRET_ACCESS_KEY'),
                'token' => self::get('AWS_SESSION_TOKEN')
            ]
        ]);
    }

    /**
     * @throws APIException|\InvalidArgumentException
     * @return KmsClient
     */
    public static function getKeyClient()
    {
        if (!self::$keyClient) {
            self::setKeyClient(self::createKeyClient());
        }

        return self::$keyClient;
    }

    /**
     * @param KmsClient $keyClient
     */
    public static function setKeyClient(KmsClient $keyClient)
    {
        self::$keyClient = $keyClient;
    }
}
