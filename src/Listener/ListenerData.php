<?php
namespace NYPL\Starter\Listener;

use NYPL\Starter\APILogger;
use NYPL\Starter\AvroDeserializer;
use NYPL\Starter\SchemaClient;

class ListenerData
{
    public $rawAvroData = '';

    public $data = [];

    public $schemaName = '';

    public $decoded = false;

    /**
     * @param string $rawAvroData
     * @param string $schemaName
     */
    public function __construct($rawAvroData = '', $schemaName = '')
    {
        $this->setRawAvroData($rawAvroData);
        $this->setSchemaName($schemaName);

        if ($schemaName) {
            $this->decodeRawData();
        }
    }

    /**
     * @param string $schemaName
     */
    public function decodeRawData($schemaName = '')
    {
        if ($schemaName) {
            $this->setSchemaName($schemaName);
        }

        APILogger::addDebug('Decoding Avro data using ' . $schemaName . ' schema');

        $this->setData(
            AvroDeserializer::deserializeWithSchema(
                SchemaClient::getSchema($this->getSchemaName()),
                $this->getRawAvroData()
            )
        );

        $this->setDecoded(true);
    }

    /**
     * @return string
     */
    public function getRawAvroData()
    {
        return $this->rawAvroData;
    }

    /**
     * @param string $rawAvroData
     */
    public function setRawAvroData($rawAvroData = '')
    {
        $this->rawAvroData = $rawAvroData;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schemaName;
    }

    /**
     * @param string $schemaName
     */
    public function setSchemaName($schemaName = '')
    {
        $this->schemaName = $schemaName;
    }

    /**
     * @return bool
     */
    public function isDecoded()
    {
        return $this->decoded;
    }

    /**
     * @param bool $decoded
     */
    public function setDecoded($decoded)
    {
        $this->decoded = (bool) $decoded;
    }
}
