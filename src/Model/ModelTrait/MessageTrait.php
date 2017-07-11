<?php
namespace NYPL\Starter\Model\ModelTrait;

use Aws\Kinesis\KinesisClient;
use Aws\Result;
use NYPL\Starter\APIException;
use NYPL\Starter\AvroLoader;
use NYPL\Starter\Config;
use NYPL\Starter\Model\ModelInterface\MessageInterface;

trait MessageTrait
{
    /**
     * @var string
     */
    protected $topic = '';

    /**
     * @var KinesisClient
     */
    protected static $client;

    /**
     * @var array
     */
    protected static $schemaCache = [];

    /**
     * @param string $topic
     * @param string $message'
     *
     * @throws \InvalidArgumentException
     */
    protected function publishMessage($topic = '', $message = '')
    {
        $this->setTopic($topic);

        $this->publishMessageAsKinesis($topic, $message);
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
            if (!$this->getTopic()) {
                $this->setTopic($model->getObjectName());
            }

            $records[] =  [
                'Data' => $model->createMessage(),
                'PartitionKey' => uniqid()
            ];
        }

        $result = self::getClient()->putRecords([
            'Records' => $records,
            'StreamName' => $this->getTopic()
        ]);

        if ($result->get('FailedRecordCount')) {
            throw new APIException(
                'Error executing Kinesis PutRecords with ' .
                $result->get('FailedRecordCount') . ' failed records',
                $this->getFailedRecords($result)
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

        foreach ((array) $result->get('Records') as $record) {
            if (isset($record['ErrorCode'])) {
                $bulkErrors[] = $record;
            }
        }

        return $bulkErrors;
    }

    /**
     * @param string $topic
     * @param string $message
     * @throws \InvalidArgumentException
     */
    protected function publishMessageAsKinesis($topic = '', $message = '')
    {
        self::getClient()->putRecord([
            'Data' => $message,
            'PartitionKey' => uniqid(),
            'StreamName' => $topic
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
        if (isset(self::$schemaCache[$this->getTopic()])) {
            return self::$schemaCache[$this->getTopic()];
        }

        /**
         * @var MessageInterface $this
         */
        $jsonSchema = json_encode($this->getSchema());

        $schema = \AvroSchema::parse($jsonSchema);

        self::$schemaCache[$this->getTopic()] = $schema;

        return $schema;
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
}
