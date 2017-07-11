<?php
namespace NYPL\KafkaStarter;

use GuzzleHttp\Client;
use NYPL\Starter\Config;
use NYPL\Starter\OAuthClient;

abstract class APIClient
{
    const CLIENT_TIMEOUT = 10;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * @param array $options
     *
     * @return array
     */
    protected static function getOptions(array $options = [])
    {
        $options['headers']['Authorization'] = 'Bearer ' . OAuthClient::getAccessToken();

        return $options;
    }

    /**
     * @return Client
     */
    protected static function getClient()
    {
        if (!self::$client) {
            self::initializeClient();
        }

        return self::$client;
    }

    /**
     * @param Client $client
     */
    protected static function setClient($client)
    {
        self::$client = $client;
    }

    protected static function initializeClient()
    {
        self::setClient(new Client([
            'base_uri' => Config::get('API_BASE_URL'),
            'timeout'  => self::CLIENT_TIMEOUT,
        ]));
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function get($uri = '', array $options = [])
    {
        return self::getClient()->get(
            $uri,
            self::getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function post($uri = '', array $options = [])
    {
        return self::getClient()->post(
            $uri,
            self::getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function put($uri = '', array $options = [])
    {
        return self::getClient()->put(
            $uri,
            self::getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function patch($uri = '', array $options = [])
    {
        return self::getClient()->patch(
            $uri,
            self::getOptions($options)
        );
    }
}
