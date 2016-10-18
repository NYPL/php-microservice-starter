<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\AvroLoader;
use NYPL\Services\Config;
use NYPL\Starter\Model\Message;
use NYPL\Starter\Model\ModelInterface\MessageInterface;

trait MessageTrait
{
    /**
     * @param string $message
     */
    protected function publishMessage($message = '')
    {
        $producer = new \RdKafka\Producer();
        $producer->setLogLevel(LOG_DEBUG);
        $producer->addBrokers(Config::MESSAGE_BROKER);

        /**
         * @var \RdKafka\ProducerTopic $topic
         */
        $topic = $producer->newTopic($this->getTableName());

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $this->getId());
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
        $jsonSchema = json_encode($this->getSchema()->getSchema());

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

    /**
     * @return string
     */
    protected function createMessageAsJson()
    {
        /**
         * @var MessageInterface $this
         */
        return json_encode(new Message(
            Message::ACTION_CREATE,
            $this->getSchema()->getId(),
            base64_encode($this->encodeMessageAsAvro())
        ));
    }
}
