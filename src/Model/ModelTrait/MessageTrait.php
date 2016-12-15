<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\AvroLoader;
use NYPL\Services\Config;
use NYPL\Starter\Model\ModelInterface\MessageInterface;

trait MessageTrait
{
    /**
     * @param string $topic
     * @param string $message
     */
    protected function publishMessage($topic = '', $message = '')
    {
        $producer = new \RdKafka\Producer();
        $producer->setLogLevel(LOG_DEBUG);
        $producer->addBrokers(Config::MESSAGE_BROKER);

        /**
         * @var \RdKafka\ProducerTopic $topic
         */
        $topic = $producer->newTopic($topic);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $this->getFullId());
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
