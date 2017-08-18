<?php
namespace NYPL\Starter;

use Aws\Kms\KmsClient;
use Dotenv\Dotenv;

class Config
{
    const LOCAL_ENVIRONMENT_FILE = '.env';
    const GLOBAL_ENVIRONMENT_FILE = 'var_app';
    const DEFAULT_TIME_ZONE = 'America/New_York';
    const CACHE_PREFIX = 'Config:';

    protected static $initialized = false;

    protected static $configDirectory = '';

    protected static $required =
        [
            'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY'
        ];

    protected static $addedRequired = [];

    /**
     * @var KmsClient
     */
    protected static $keyClient;

    /**
     * @param string $configDirectory
     * @param array $required
     * @throws APIException
     */
    public static function initialize($configDirectory = '', array $required = [])
    {
        self::setConfigDirectory($configDirectory);

        if ($required) {
            self::setAddedRequired($required);
        }

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
     * @return null|string
     * @throws APIException
     */
    public static function get($name = '', $defaultValue = null, $isEncrypted = false)
    {
        if (!self::isInitialized()) {
            throw new APIException('Configuration has not been initialized');
        }

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
     * @return string
     */
    protected static function decryptEnvironmentVariable($name = '')
    {
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

    protected static function loadConfiguration()
    {
        if (file_exists(self::getConfigDirectory() . '/' . self::LOCAL_ENVIRONMENT_FILE)) {
            $dotEnv = new Dotenv(self::getConfigDirectory(), self::LOCAL_ENVIRONMENT_FILE);
            $dotEnv->load();
        }

        if (file_exists(self::getConfigDirectory() . '/config/' . self::GLOBAL_ENVIRONMENT_FILE)) {
            $dotEnv = new Dotenv(self::getConfigDirectory() . '/config', self::GLOBAL_ENVIRONMENT_FILE);
            $dotEnv->load();
        }

        $dotEnv->required(self::getRequired());

        $dotEnv->required(self::getAddedRequired());

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
    public static function getRequired()
    {
        return self::$required;
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
