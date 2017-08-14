<?php
namespace NYPL\Starter\Listener;

use NYPL\Starter\APILogger;

abstract class ListenerEvents
{
    /**
     * @param array $record
     */
    abstract public function translateEvents(array $record);

    /**
     * @param array $record
     * @param string $schemaName
     *
     * @return ListenerEvent
     */
    abstract public function translateEvent(array $record, $schemaName = '');

    /**
     * @var bool
     */
    public $initialized = false;

    /**
     * @var ListenerEvent[]
     */
    public $events;

    /**
     * @return ListenerEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param ListenerEvent[] $events
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @param bool $initialized
     */
    public function setInitialized($initialized)
    {
        $this->initialized = (bool) $initialized;
    }

    /**
     * @param array $record
     * @param string $schemaName
     */
    public function addEvent(array $record, $schemaName = '')
    {
        if (!$this->isInitialized()) {
            $this->initializeEvents($record);
        }

        APILogger::addDebug('Adding event');

        $this->events[] = $this->translateEvent(
            $record,
            $schemaName
        );
    }

    /**
     * @param array $record
     */
    public function initializeEvents(array $record)
    {
        APILogger::addDebug('Initializing events');

        $this->translateEvents($record);

        $this->setInitialized(true);
    }
}
