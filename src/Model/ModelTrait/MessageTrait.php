<?php
namespace NYPL\Starter\Model\ModelTrait;

use Aws\Kinesis\KinesisClient;
use NYPL\Starter\AvroLoader;
use NYPL\Starter\Config;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use RdKafka\Producer;

trait MessageTrait
{
    /**
     * @param string $topic
     * @param string $message
     */
    protected function publishMessage($topic = '', $message = '')
    {
        $this->publishMessageAsKinesis($topic, $message);
    }

    /**
     * @param string $topic
     * @param string $message
     */
    protected function publishMessageAsKafka($topic = '', $message = '')
    {
        $producer = new Producer();
        $producer->setLogLevel(LOG_DEBUG);
        $producer->addBrokers(Config::get('MESSAGE_BROKER'));

        /**
         * @var \RdKafka\ProducerTopic $topic
         */
        $topic = $producer->newTopic($topic);

        $topic->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            $message,
            $this->getFullId()
        );
    }

    /**
     * @param string $topic
     * @param string $message
     * @throws \InvalidArgumentException
     */
    protected function publishMessageAsKinesis($topic = '', $message = '')
    {
        $client = new KinesisClient([
            'version' => 'latest',
            'region'  => Config::get('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => Config::get('AWS_ACCESS_KEY_ID'),
                'secret' => Config::get('AWS_SECRET_ACCESS_KEY'),
                'token' => Config::get('AWS_SESSION_TOKEN')
            ]
        ]);

        $client->putRecord([
            'Data' => $message,
            'PartitionKey' => md5($topic),
            'StreamName' => $topic
        ]);
    }

    /**
     * @return string
     */
    protected function encodeMessageAsAvro()
    {
        AvroLoader::load();

        /**
         * @var MessageInterface $this
         */
        $jsonSchema = json_encode($this->getSchema());

        $schema = \AvroSchema::parse($jsonSchema);

        $io = new \AvroStringIO();
        $writer = new \AvroIODatumWriter($schema);
        $encoder = new \AvroIOBinaryEncoder($io);

        $dataArray = json_decode(json_encode($this), true);

        $writer->write($dataArray, $encoder);

        return $io->string();
    }

    protected function createMessage()
    {
        return $this->createMessageAsBinary();
    }

    /**
     * @return string
     */
    protected function createMessageAsBinary()
    {
        /**
         * @var MessageInterface $this
         */
        return $this->encodeMessageAsAvro();
    }

}
