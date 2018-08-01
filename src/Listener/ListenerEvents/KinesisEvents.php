<?php
namespace NYPL\Starter\Listener\ListenerEvents;

use NYPL\Starter\APIException;
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
     * @param array $payload
     *
     * @return string
     * @throws APIException
     */
    public static function getStreamNameFromPayLoad($payload = [])
    {
        $kinesisEvent = new KinesisEvents();

        if (!isset($payload['eventSourceARN'])) {
            throw new APIException('Unable to get Event Source ARN from specific event payment');
        }

        return $kinesisEvent->getStreamNameFromArn($payload['eventSourceARN']);
    }

    /**
     * @param array $record
     *
     * @throws APIException
     */
    public function translateEvents(array $record)
    {
        if (!isset($record['eventSourceARN'])) {
            throw new APIException('Unable to get Event Source ARN from events payment');
        }

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
    public function getEventSourceARN()
    {
        return $this->eventSourceARN;
    }

    /**
     * @param string $eventSourceARN
     */
    public function setEventSourceARN($eventSourceARN = '')
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
