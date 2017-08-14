<?php
namespace NYPL\Starter\Listener\ListenerEvents;

use NYPL\Starter\APILogger;
use NYPL\Starter\Listener\ListenerData;
use NYPL\Starter\Listener\ListenerEvent\KinesisEvent;
use NYPL\Starter\Listener\ListenerEvents;

class KinesisEvents extends ListenerEvents
{
    /**
     * @var string
     */
    public $eventSourceARN = '';

    /**
     * @var string
     */
    public $streamName = '';

    /**
     * @param array $record
     */
    public function translateEvents(array $record)
    {
        $this->setEventSourceARN($record['eventSourceARN']);
    }

    /**
     * @param array $record
     * @param string $schemaName
     * @return KinesisEvent
     */
    public function translateEvent(array $record, $schemaName = '')
    {
        $kinesisEvent = new KinesisEvent(
            new ListenerData(
                base64_decode($record['kinesis']['data']),
                $schemaName
            )
        );

        $kinesisEvent->setSequenceNumber($record['kinesis']['sequenceNumber']);

        return $kinesisEvent;
    }

    /**
     * @return string
     */
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * @param string $streamName
     */
    public function setStreamName($streamName)
    {
        $this->streamName = $streamName;
    }

    /**
     * @return string
     */
    public function getEventSourceARN(): string
    {
        return $this->eventSourceARN;
    }

    /**
     * @param string $eventSourceARN
     */
    public function setEventSourceARN(string $eventSourceARN)
    {
        $this->eventSourceARN = $eventSourceARN;

        $this->setStreamName(
            $this->getStreamNameFromArn($eventSourceARN)
        );
    }

    /**
     * @param string $streamArn
     *
     * @return string
     */
    protected function getStreamNameFromArn($streamArn = '')
    {
        $streamComponents = explode('/', $streamArn);

        $streamName = $streamComponents[count($streamComponents) - 1];

        APILogger::addDebug(
            'Processing record in ' . $streamName . ' stream.'
        );

        return $streamName;
    }
}
