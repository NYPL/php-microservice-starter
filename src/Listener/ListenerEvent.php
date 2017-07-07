<?php
namespace NYPL\Starter\Listener;

abstract class ListenerEvent
{
    /**
     * @var ListenerData
     */
    public $listenerData;

    /**
     * @param ListenerData $listenerData
     */
    public function __construct(ListenerData $listenerData)
    {
        $this->setListenerData($listenerData);
    }

    /**
     * @return ListenerData
     */
    public function getListenerData()
    {
        return $this->listenerData;
    }

    /**
     * @param ListenerData $listenerData
     */
    public function setListenerData(ListenerData $listenerData)
    {
        $this->listenerData = $listenerData;
    }
}
