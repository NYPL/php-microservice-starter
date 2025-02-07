<?php
namespace NYPL\Starter;

use Aws\Kms\KmsClient;
use Dotenv\Dotenv;

class Config
{
    protected const ENV_NAME_LOCAL = 'local';

    protected const ENV_NAME_QA = 'qa';

    protected const ENV_NAME_PROD = 'production';

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
     * Get Environment from Environment variable 'ENVIRONMENT'. Defaults to "local".
     *
     * @return string|null
     * @throws APIException
     */
    public static function getEnvironment() {
        return getenv('ENVIRONMENT') ?: self::ENV_NAME_LOCAL;
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
        if (getenv($name)) {
            if ($isEncrypted && self::isEncryptedEnvironment()) {
                return self::decryptEnvironmentVariable($name);
            }

            return (string) getenv($name);
        }

        return $defaultValue;
    }

    /**
     * Returns true if environment is local or not defined.
     *
     * @throws APIException
     * @return bool
     */
    public static function isLocalEnvironment()
    {
        return self::getEnvironment() == self::ENV_NAME_LOCAL;
    }

    /**
     * Returns true if environment is production or qa.
     *
     * @return bool
     * @throws APIException
     */
    public static function isProductionEnvironment()
    {
        if (in_array(self::getEnvironment(), [self::ENV_NAME_QA, self::ENV_NAME_PROD])) {
            return true;
        }

        return false;
    }

    /**
     * @throws APIException
     * @return bool
     */
    protected static function isEncryptedEnvironment()
    {
        return getenv('DECRYPT_ENV_VARS', true);
    }

    /**
     * @param string $name
     *
     * @throws APIException|\InvalidArgumentException
     * @return string
     */
    protected static function decryptEnvironmentVariable($name = '') {
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
     * Loads Global Environment file first, then environment file for current environment, allowing overrides.
     *
     * @throws APIException
     */
    protected static function loadConfiguration()
    {
        // Load Global config.
        if (file_exists(self::getConfigDirectory() . '/' . self::GLOBAL_ENVIRONMENT_FILE)) {
            $dotEnv = Dotenv::createUnsafeMutable(self::getConfigDirectory(), self::GLOBAL_ENVIRONMENT_FILE);
            $dotEnv->load();
        }

        $environmentFile = self::getEnvironment() . '.env';
        if (!file_exists(self::getConfigDirectory() . '/' . $environmentFile)) {
            throw new APIException(
                'Unable to load environment configuration file (' . $environmentFile . ')'
            );
        }

        // The method createUnsafeMutable() is used here instead of createMutable() because it enables getenv() to be
        // used for fetching the loaded env vars. The preferred, thread-safe, practice is to fetch env vars directly
        // from the $_ENV global array instead of using getenv(), however, we want to allow the use of getenv() to
        // support legacy code.
        $dotEnv = Dotenv::createUnsafeMutable(self::getConfigDirectory(), $environmentFile);
        $dotEnv->load();

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
