<?php
namespace NYPL\Starter;

class AvroDeserializer
{
    /**
     * @var bool
     */
    protected static $initialized = false;

    /**
     * @var \AvroStringIO[]
     */
    protected static $avroIoCache;

    /**
     * @var \AvroIODatumReader[]
     */
    protected static $avroReaderCache;

    /**
     * @var \AvroIOBinaryDecoder[]
     */
    protected static $avroDecoderCache;

    /**
     * @return bool
     */
    protected static function initialize()
    {
        if (!self::isInitialized()) {
            AvroLoader::load();

            self::setInitialized(true);
        }

        return true;
    }

    /**
     * @param string $topic
     * @param int $offset
     * @param mixed $payload
     *
     * @return array|string
     */
    public static function deserialize($topic = '', $offset = 0, $payload = null)
    {
        self::initialize();

        $schema = SchemaClient::getSchema(
            $topic,
            $offset
        );

        return self::deserializeWithSchema($schema, $payload);
    }

    /**
     * @param Schema $schema
     * @param mixed $payload
     *
     * @return array|string
     */
    public static function deserializeWithSchema(Schema $schema, $payload = null)
    {
        $avroIo = self::getAvroIo($schema);
        $avroReader = self::getAvroReader($schema);
        $decoder = self::getAvroDecoder($schema);

        $avroIo->truncate();
        $avroIo->write($payload);
        $avroIo->seek(0);

        return $avroReader->read($decoder);
    }

    /**
     * @return boolean
     */
    public static function isInitialized()
    {
        return self::$initialized;
    }

    /**
     * @param boolean $initialized
     */
    public static function setInitialized($initialized)
    {
        self::$initialized = $initialized;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroStringIO
     */
    protected static function getAvroIo(Schema $schema)
    {
        if ($avroIo = self::getCachedAvroIo($schema)) {
            return $avroIo;
        }

        $avroIo = new \AvroStringIO();

        self::addAvroIoToCache($schema, $avroIo);

        return $avroIo;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroStringIO|null
     */
    protected static function getCachedAvroIo(Schema $schema)
    {
        if (isset(self::$avroIoCache[$schema->getCacheKey()])) {
            return self::$avroIoCache[$schema->getCacheKey()];
        }

        return null;
    }

    /**
     * @param Schema $schema
     * @param \AvroStringIO $avroIo
     */
    protected static function addAvroIoToCache(Schema $schema, \AvroStringIO $avroIo)
    {
        self::$avroIoCache[$schema->getCacheKey()] = $avroIo;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroIODatumReader|null
     */
    protected static function getAvroReader(Schema $schema)
    {
        if ($avroReader = self::getCachedAvroReader($schema)) {
            return $avroReader;
        }

        $avroReader = new \AvroIODatumReader($schema->getAvroSchema());

        self::addAvroReaderToCache($schema, $avroReader);

        return $avroReader;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroIODatumReader|null
     */
    protected static function getCachedAvroReader(Schema $schema)
    {
        if (isset(self::$avroReaderCache[$schema->getCacheKey()])) {
            return self::$avroReaderCache[$schema->getCacheKey()];
        }

        return null;
    }

    /**
     * @param Schema $schema
     * @param \AvroIODatumReader $avroReader
     */
    protected static function addAvroReaderToCache(Schema $schema, \AvroIODatumReader $avroReader)
    {
        self::$avroReaderCache[$schema->getCacheKey()] = $avroReader;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroIOBinaryDecoder|null
     */
    protected static function getAvroDecoder(Schema $schema)
    {
        if ($avroDecoder = self::getCachedAvroDecoder($schema)) {
            return $avroDecoder;
        }

        $avroDecoder = new \AvroIOBinaryDecoder(self::getAvroIo($schema));

        self::addAvroDecoderToCache($schema, $avroDecoder);

        return $avroDecoder;
    }

    /**
     * @param Schema $schema
     *
     * @return \AvroIOBinaryDecoder|null
     */
    protected static function getCachedAvroDecoder(Schema $schema)
    {
        if (isset(self::$avroDecoderCache[$schema->getCacheKey()])) {
            return self::$avroDecoderCache[$schema->getCacheKey()];
        }

        return null;
    }

    /**
     * @param Schema $schema
     * @param \AvroIOBinaryDecoder $avroDecoder
     */
    protected static function addAvroDecoderToCache(Schema $schema, \AvroIOBinaryDecoder $avroDecoder)
    {
        self::$avroDecoderCache[$schema->getCacheKey()] = $avroDecoder;
    }
}
