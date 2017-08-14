<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

abstract class APIClient
{
    const CLIENT_TIMEOUT = 10;

    /**
     * @var Client
     */
    protected $client;

    protected $isRequiresAuth = false;

    /**
     * @return bool
     */
    abstract protected function isRequiresAuth();

    /**
     * @param array $options
     *
     * @return array
     */
    protected function getOptions(array $options = [])
    {
        if ($this->isRequiresAuth()) {
            $options['headers']['Authorization'] = 'Bearer ' . OAuthClient::getAccessToken();
        }

        return $options;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->initializeClient();
        }

        return $this->client;
    }

    /**
     * @param Client $client
     */
    protected function setClient($client)
    {
        $this->client = $client;
    }

    protected function initializeClient()
    {
        $this->setClient(new Client([
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
    public function get($uri = '', array $options = [])
    {
        return $this->getClient()->get(
            $uri,
            $this->getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($uri = '', array $options = [])
    {
        return $this->getClient()->post(
            $uri,
            $this->getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($uri = '', array $options = [])
    {
        return $this->getClient()->put(
            $uri,
            $this->getOptions($options)
        );
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function patch($uri = '', array $options = [])
    {
        return $this->getClient()->patch(
            $uri,
            $this->getOptions($options)
        );
    }
}
