<?php
namespace NYPL\Starter\Listener\ListenerEvent;

use NYPL\Starter\Listener\ListenerEvent;

class KinesisEvent extends ListenerEvent
{
    public $sequenceNumber = '';

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @param string $sequenceNumber
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->sequenceNumber = $sequenceNumber;
    }
}
