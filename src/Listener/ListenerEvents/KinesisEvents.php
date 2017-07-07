<?php
namespace NYPL\Starter\Listener\ListenerEvents;

use NYPL\Starter\Listener\ListenerEvents;

class KinesisEvents extends ListenerEvents
{
    /**
     * @var string
     */
    public $streamName = '';

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
}
