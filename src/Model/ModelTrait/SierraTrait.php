<?php
namespace NYPL\Starter\Model\ModelTrait;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use NYPL\Starter\Config;
use NYPL\Starter\APIException;
use NYPL\Starter\DB;

trait SierraTrait
{
    /**
     * @param string $id
     *
     * @return string
     */
    abstract public function getSierraPath($id = '');

    /**
     * @return string
     */
    abstract public function getRequestType();

    /**
     * @param string $id
     *
     * @return string
     */
    public function getSierraId($id = '')
    {
        if (is_numeric(substr($id, 0, 1))) {
            return $id;
        }

        return substr($id, 1);
    }

    /**
     * @param string $path
     * @param bool $ignoreNoRecord
     * @param array $headers
     *
     * @return string
     * @throws APIException
     */
    protected function sendRequest($path = '', $ignoreNoRecord = false, array $headers = [])
    {
        $client = new Client();

        $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();

        try {
            $request = $client->request(
                $this->getRequestType(),
                Config::get('SIERRA_BASE_API_URL') . '/' . $path,
                [
                    'verify' => false,
                    'headers' => $headers,
                    'body' => $this->getBody()
                ]
            );
        } catch (ClientException $clientException) {
            if (!$ignoreNoRecord) {
                throw new APIException(
                    (string) $clientException->getMessage(),
                    null,
                    0,
                    null,
                    $clientException->getResponse()->getStatusCode()
                );
            }
        }

        return (string) $request->getBody();
    }

    /**
     * @param array $token
     */
    protected function saveToken(array $token = [])
    {
        $token["expire_time"] = time() + $token["expires_in"];

        $insertStatement = DB::getDatabase()->insert(array_keys($token))
            ->into("Token")
            ->values(array_values($token));

        $insertStatement->execute(true);
    }

    /**
     * @return string
     */
    protected function getAccessToken()
    {
        $selectStatement = DB::getDatabase()->select()
            ->from("Token")
            ->where("expire_time", ">", time());

        $selectStatement = $selectStatement->execute();

        if ($selectStatement->rowCount()) {
            $token = $selectStatement->fetch();

            return $token['access_token'];
        }

        $token = json_decode($this->getNewToken(), true);

        $this->saveToken($token);

        return $token["access_token"];
    }

    /**
     * @return string
     */
    protected function getNewToken()
    {
        $client = new Client();

        $request = $client->request(
            'POST',
            Config::get('SIERRA_OAUTH_TOKEN_URI'),
            [
                'auth' => [
                    Config::get('SIERRA_OAUTH_CLIENT_ID'),
                    Config::get('SIERRA_OAUTH_CLIENT_SECRET')
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ],
                'verify' => false
            ]
        );

        return (string) $request->getBody();
    }
}
