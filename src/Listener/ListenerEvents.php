<?php
namespace NYPL\Starter\Listener;

abstract class ListenerEvents
{
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
}
