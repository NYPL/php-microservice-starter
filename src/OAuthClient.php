<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

class OAuthClient
{
    const CACHE_KEY = 'OAuthClient:Token';

    /**
     * @return string
     */
    public static function getAccessToken()
    {
        if ($accessToken = AppCache::get(self::CACHE_KEY)) {
            $accessToken = unserialize($accessToken);

            return $accessToken['access_token'];
        }

        $accessToken = self::retrieveAccessToken();

        AppCache::set(
            self::CACHE_KEY,
            serialize($accessToken),
            $accessToken['expires_in'] - 60
        );

        return $accessToken['access_token'];
    }

    /**
     * @return array
     * @throws APIException
     */
    protected static function retrieveAccessToken()
    {
        $client = new Client();

        if (!Config::get('OAUTH_TOKEN_URI')) {
            throw new APIException('OAUTH_TOKEN_URI was not specified');
        }

        if (!Config::get('OAUTH_CLIENT_ID') || !Config::get('OAUTH_CLIENT_SECRET')) {
            throw new APIException('OAUTH_CLIENT_ID or OAUTH_CLIENT_SECRET was not specified');
        }

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

        return json_decode($response->getBody(), true);
    }
}
