<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

class SchemaClient
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var array
     */
    protected static $schemaCache = [];

    /**
     * @return Client
     */
    public static function getClient()
    {
        if (!self::$client) {
            self::setClient(
                new Client()
            );
        }

        return self::$client;
    }

    /**
     * @param Client $client
     */
    public static function setClient($client)
    {
        self::$client= $client;
    }

    /**
     * @param string $schemaName
     *
     * @return Schema
     */
    public static function getSchema($schemaName = '')
    {
        if (isset(self::$schemaCache[$schemaName])) {
            return self::$schemaCache[$schemaName];
        }

        AvroLoader::load();

        $response = json_decode(
            self::getClient()->get(Config::get('SCHEMA_BASE_URL') . '/' . $schemaName)->getBody(),
            true
        );

        $schema = new Schema(
            $schemaName,
            0,
            \AvroSchema::parse($response['data']['schema']),
            $response['data']['schemaObject']
        );

        self::$schemaCache[$schemaName] = $schema;

        APILogger::addDebug(
            'Got schema for ' . $schemaName,
            (array) $schema->getSchema()
        );

        return $schema;
    }
}
