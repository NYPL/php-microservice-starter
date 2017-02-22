<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

class JobManager
{
    /**
     * @return string
     */
    public static function createJob()
    {
        if (Config::get('JOB_SERVICE_URL')) {
            $client = new Client([
                'base_uri' => Config::get('JOB_SERVICE_URL'),
                'timeout'  => 10,
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
        return Config::get('JOB_SERVICE_URL') . '/' . $id;
    }
}
