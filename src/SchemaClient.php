<?php
namespace NYPL\Starter;

use GuzzleHttp\Client;

class SchemaClient
{
    const BASE_CACHE_KEY = 'SchemaClient:';
    const DEFAULT_SCHEMA_EXPIRATION_SECONDS = 360;

    /**
     * @var Client
     */
    protected static $client;

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
     * @return array
     */
    protected static function getSchemaResponse($schemaName = '')
    {
        return json_decode(
            self::getClient()->get(Config::get('SCHEMA_BASE_URL') . '/' . $schemaName)->getBody(),
            true
        );
    }

    /**
     * @param string $schemaName
     *
     * @return Schema
     */
    public static function getSchema($schemaName = '')
    {
        AvroLoader::load();

        $cacheKey = self::BASE_CACHE_KEY . 'Schema:' . $schemaName;

        if ($schema = AppCache::get($cacheKey)) {
            return unserialize($schema);
        }

        $response = self::getSchemaResponse($schemaName);

        $schema = new Schema(
            $schemaName,
            0,
            \AvroSchema::parse($response['data']['schema']),
            $response['data']['schemaObject']
        );

        AppCache::set(
            $cacheKey,
            serialize($schema),
            self::DEFAULT_SCHEMA_EXPIRATION_SECONDS
        );

        APILogger::addDebug(
            'Got schema for ' . $schemaName,
            (array) $schema->getSchema()
        );

        return $schema;
    }
}
