<?php
namespace NYPL\Starter\Listener;

abstract class ListenerEvents
{
    /**
     * @param array $record
     */
    abstract public function initializeEvents(array $record);

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
     * @param ListenerEvent $listenerEvent
     */
    public function addEvent(ListenerEvent $listenerEvent)
    {
        $this->events[] = $listenerEvent;
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
}
