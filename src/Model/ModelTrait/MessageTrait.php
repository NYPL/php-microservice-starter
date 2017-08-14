<?php
namespace NYPL\Starter\Model\ModelTrait;

use Aws\Kinesis\KinesisClient;
use Aws\Result;
use NYPL\Starter\APIException;
use NYPL\Starter\APILogger;
use NYPL\Starter\AvroLoader;
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
     * @throws \AvroIOException|\InvalidArgumentException|APIException
     */
    protected function bulkPublishMessages(array $models = [])
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

        $result = self::getClient()->putRecords([
            'Records' => $records,
            'StreamName' => $model->getStreamName()
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
     * @throws \InvalidArgumentException
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
     * @throws \AvroIOException
     * @return string
     */
    protected function encodeMessageAsAvro()
    {
        AvroLoader::load();

        $io = new \AvroStringIO();
        $writer = new \AvroIODatumWriter($this->getAvroSchema());
        $encoder = new \AvroIOBinaryEncoder($io);

        $dataArray = json_decode(json_encode($this), true);

        $writer->write($dataArray, $encoder);

        return $io->string();
    }

    /**
     * @throws \AvroIOException
     * @return string
     */
    public function createMessage()
    {
        /**
         * @var MessageInterface $this
         */
        return $this->encodeMessageAsAvro();
    }

    /**
     * @throws \InvalidArgumentException
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
     * @return \AvroSchema
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

        $schema = \AvroSchema::parse($jsonSchema);

        self::$schemaCache[$this->getStreamName()] = $schema;

        return $schema;
    }

    /**
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
