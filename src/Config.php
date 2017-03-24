<?php
namespace NYPL\Starter;

use Aws\Kms\KmsClient;
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

    protected static $environmentName = 'ENVIRONMENT';

    protected static $localEnvironmentValue = 'local';

    protected static $addedRequired = [];

    /**
     * @var KmsClient
     */
    protected static $keyClient;

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
     * @return bool
     */
    protected static function isEncryptedEnvironment()
    {
        if (self::get(self::$environmentName) == self::$localEnvironmentValue) {
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected static function decryptEnvironmentVariable($name = '')
    {
        return (string) self::getKeyClient()->decrypt([
            'CiphertextBlob' => base64_decode(getenv($name)),
        ])['Plaintext'];
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
    protected static function isInitialized()
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

    /**
     * @return KmsClient
     */
    protected static function createKeyClient()
    {
        return new KmsClient([
            'version' => 'latest',
            'region'  => Config::get('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => Config::get('AWS_ACCESS_KEY_ID'),
                'secret' => Config::get('AWS_SECRET_ACCESS_KEY'),
                'token' => Config::get('AWS_SESSION_TOKEN')
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
