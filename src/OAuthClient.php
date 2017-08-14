<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

class OAuthClient
{
    /**
     * @var string
     */
    public static $accessToken = '';

    /**
     * @return string
     */
    public static function getAccessToken()
    {
        if (!self::$accessToken) {
            $accessToken = self::initializeAccessToken();

            self::setAccessToken($accessToken);
        }

        return self::$accessToken;
    }

    /**
     * @param string $accessToken
     */
    public static function setAccessToken($accessToken = '')
    {
        self::$accessToken = $accessToken;
    }

    protected static function initializeAccessToken()
    {
        $client = new Client();

        $response = $client->post(
            Config::get('OAUTH_TOKEN_URI'),
            [
                'form_params' => [
                    'client_id' => Config::get('OAUTH_CLIENT_ID', null, true),
                    'client_secret' => Config::get('OAUTH_CLIENT_SECRET', null, true),
                    'grant_type' => 'client_credentials',
                    'scope' => Config::get('OAUTH_CLIENT_SCOPES'),
                ]
            ]
        );

        return json_decode($response->getBody(), true)['access_token'];
    }
}
