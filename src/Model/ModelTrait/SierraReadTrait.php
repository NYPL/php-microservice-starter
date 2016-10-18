<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Services\Config;
use NYPL\Starter\DB;
use NYPL\Starter\Model;

trait SierraReadTrait
{
    /**
     * @return string
     */
    abstract public function getSierraPath($id = '');

    /**
     * @param string $path
     * @param array $headers
     *
     * @return resource
     */
    protected function getCurl($path = '', array $headers = [])
    {
        $curl = curl_init();

        $headers[] = 'Authorization: Bearer ' . $this->getAccessToken();

        curl_setopt($curl, CURLOPT_URL, Config::BASE_SIERRA_API_URL . '/' . $path);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        return $response;
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


    protected function getNewToken()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, Config::OAUTH_TOKEN_URI);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ["grant_type" => "client_credentials"]);
        curl_setopt($curl, CURLOPT_USERPWD, Config::OAUTH_CLIENT_ID . ":" . Config::OAUTH_CLIENT_SECRET);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $tokenJson = curl_exec($curl);

        curl_close($curl);

        return $tokenJson;
    }

    public function read($id = '')
    {
        $response = $this->getCurl($this->getSierraPath($id));

        $data = json_decode($response, true);

        if (isset($data['httpStatus'])) {
            throw new APIException($data['name'], $data);
        }

        $this->translate($data);

        return true;
    }
}
