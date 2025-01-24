<?php
namespace NYPL\Starter\Model\ModelTrait;

use Avro\Datum\IOBinaryEncoder;
use Avro\Datum\IODatumWriter;
use Avro\Exception\IOException;
use Avro\IO\StringIO;
use Avro\Schema\Schema;
use Aws\Kinesis\KinesisClient;
use Aws\Result;
use NYPL\Starter\APIException;
use NYPL\Starter\APILogger;
use NYPL\Starter\Config;
use NYPL\Starter\Model\ModelInterface\MessageInterface;

trait MessageTrait
{
    /**
     * @var string
     */
    protected $streamName = '';

    /**
     * @var KinesisClient
     */
    protected static $client;

    /**
     * @var array
     */
    protected static $schemaCache = [];

    /**
     * @var array
     */
    protected static $avroCache = [];

    /**
     * @param string $streamName
     * @param string $message'
     *
     * @throws \InvalidArgumentException
     */
    protected function publishMessage($streamName = '', $message = '')
    {
        $this->setStreamName($streamName);

        $this->publishMessageAsKinesis($streamName, $message);
    }

    /**
     * @param array $models
     * @param string $streamName
     * @throws IOException|\InvalidArgumentException|APIException
     */
    protected function bulkPublishMessages(array $models = [], $streamName = '')
    {
        $records = [];

        /**
         * @var $model MessageTrait
         */
        foreach ($models as $model) {
            $records[] =  [
                'Data' => $model->createMessage(),
                'PartitionKey' => uniqid()
            ];
        }

        if (!$streamName) {
            $streamName = $model->getStreamName();
        }

        $result = self::getClient()->putRecords([
            'Records' => $records,
            'StreamName' => $streamName
        ]);

        if ($result->get('FailedRecordCount')) {
            APILogger::addError(
                'Failed PutRecords',
                $this->getFailedRecords($result)
            );

            throw new APIException(
                'Error executing Kinesis PutRecords with ' .
                $result->get('FailedRecordCount') . ' failed records'
            );
        }

        if (count($records) !== count($result->get('Records'))) {
            throw new APIException(
                'Mismatched count in Kinesis PutRecords: expected ' .
                count($records) . ' and got ' . count($result->get('Records'))
            );
        }
    }

    /**
     * @param Result $result
     *
     * @return array
     */
    protected function getFailedRecords(Result $result)
    {
        $bulkErrors = [];

        foreach ((array) $result->get('Records') as $result) {
            if (isset($result['ErrorCode'])) {
                $bulkErrors[] = $result;
            }
        }

        return $bulkErrors;
    }

    /**
     * @param string $streamName
     * @param string $message
     *
     * @throws \InvalidArgumentException|APIException
     */
    protected function publishMessageAsKinesis($streamName = '', $message = '')
    {
        self::getClient()->putRecord([
            'Data' => $message,
            'PartitionKey' => uniqid(),
            'StreamName' => $streamName
        ]);
    }

    /**
     * @return StringIO
     */
    protected function getAvroIo()
    {
        $streamName = $this->getStreamName();

        if (isset(self::$avroCache[$streamName]['io'])) {
            return self::$avroCache[$streamName]['io'];
        }

        $io = new StringIO();

        self::$avroCache[$streamName]['io'] = $io;

        return $io;
    }

    /**
     * @return IODatumWriter
     */
    protected function getAvroWriter()
    {
        $streamName = $this->getStreamName();

        if (isset(self::$avroCache[$streamName]['writer'])) {
            return self::$avroCache[$streamName]['writer'];
        }

        $writer = new IODatumWriter($this->getAvroSchema());

        self::$avroCache[$streamName]['writer'] = $writer;

        return $writer;
    }

    /**
     * @return IOBinaryEncoder
     */
    protected function getAvroEncoder()
    {
        $streamName = $this->getStreamName();

        if (isset(self::$avroCache[$streamName]['encoder'])) {
            return self::$avroCache[$streamName]['encoder'];
        }

        $encoder = new IOBinaryEncoder(self::getAvroIo());

        self::$avroCache[$streamName]['encoder'] = $encoder;

        return $encoder;
    }

    /**
     * @return string
     */
    protected function encodeMessageAsAvro()
    {
        self::getAvroWriter()->write(
            json_decode(json_encode($this), true),
            self::getAvroEncoder()
        );

        $encodedString = self::getAvroIo()->string();

        self::getAvroIo()->truncate();

        return $encodedString;
    }

    /**
     * @throws IOException
     * @return string
     */
    public function createMessage()
    {
        return $this->encodeMessageAsAvro();
    }

    /**
     * @throws \InvalidArgumentException|APIException
     * @return KinesisClient
     */
    public static function getClient()
    {
        if (!self::$client) {
            self::setClient(
                new KinesisClient([
                    'version' => 'latest',
                    'region'  => Config::get('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => Config::get('AWS_ACCESS_KEY_ID'),
                        'secret' => Config::get('AWS_SECRET_ACCESS_KEY'),
                        'token' => Config::get('AWS_SESSION_TOKEN')
                    ]
                ])
            );
        }

        return self::$client;
    }

    /**
     * @param KinesisClient $client
     */
    public static function setClient($client)
    {
        self::$client = $client;
    }

    /**
     * @return Schema
     */
    public function getAvroSchema()
    {
        if (isset(self::$schemaCache[$this->getStreamName()])) {
            return self::$schemaCache[$this->getStreamName()];
        }

        /**
         * @var MessageInterface $this
         */
        $jsonSchema = json_encode($this->getSchema());

        $schema = Schema::parse($jsonSchema);

        self::$schemaCache[$this->getStreamName()] = $schema;

        return $schema;
    }

    /**
     * @throws APIException
     * @return string
     */
    public function getStreamName()
    {
        if (!$this->streamName) {
            $this->setStreamName(
                Config::get('DEFAULT_STREAM', $this->getObjectName())
            );
        }

        return $this->streamName;
    }

    /**
     * @param string $streamName
     */
    public function setStreamName($streamName)
    {
        $this->streamName = $streamName;
    }
}
