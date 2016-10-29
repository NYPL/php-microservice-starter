<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;
use NYPL\Services\Config;

class JobManager
{
    /**
     * @return string
     */
    public static function createJob()
    {
        if (Config::JOB_SERVICE_URL) {
            $client = new Client([
                'base_uri' => Config::JOB_SERVICE_URL,
                'timeout'  => 2.0,
            ]);

            $response = $client->post('');

            $body = json_decode($response->getBody(), true);

            return (string) $body['data']['id'];
        }
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public static function getJobUrl($id = '')
    {
        return Config::JOB_SERVICE_URL . '/' . $id;
    }
}
