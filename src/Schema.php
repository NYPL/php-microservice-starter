<?php
namespace NYPL\Starter;

class Schema
{
    protected $topic = '';

    protected $offset = 0;

    /**
     * @var \AvroSchema
     */
    protected $avroSchema;

    /**
     * @var int
     */
    protected $schemaId = 0;

    /**
     * @var array
     */
    protected $schema = [];

    /**
     * @param string $topic
     * @param int $offset
     * @param \AvroSchema|null $avroSchema
     * @param array $schema
     */
    public function __construct($topic = '', $offset = 0, \AvroSchema $avroSchema = null, array $schema = [])
    {
        if ($topic) {
            $this->setTopic($topic);
        }

        if ($offset) {
            $this->setOffset($offset);
        }

        if ($avroSchema) {
            $this->setAvroSchema($avroSchema);
        }

        if ($schema) {
            $this->setSchema($schema);
        }
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * @return \AvroSchema
     */
    public function getAvroSchema()
    {
        return $this->avroSchema;
    }

    /**
     * @param \AvroSchema $avroSchema
     */
    public function setAvroSchema(\AvroSchema $avroSchema)
    {
        $this->avroSchema = $avroSchema;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->getSchemaId();
    }

    /**
     * @return int
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * @param int $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = $schemaId;
    }

    /**
     * @return array
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param array $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }
}
