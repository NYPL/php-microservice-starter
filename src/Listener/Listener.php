<?php
namespace NYPL\Starter\Listener;

use NYPL\Starter\APIException;
use NYPL\Starter\APILogger;
use NYPL\Starter\ErrorHandler;

abstract class Listener
{
    /**
     * @var ListenerEvents
     */
    protected $listenerEvents;

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * Listener constructor.
     * @throws APIException
     */
    public function __construct()
    {
        set_error_handler(ErrorHandler::class . '::errorFunction');

        $this->initializePayload();
    }

    /**
     * @return ListenerResult
     */
    abstract protected function processListenerEvents();

    /**
     * @return ListenerEvents
     */
    protected function getListenerEvents()
    {
        return $this->listenerEvents;
    }

    /**
     * @param ListenerEvents $listenerEvents
     */
    protected function setListenerEvents(ListenerEvents $listenerEvents)
    {
        $this->listenerEvents = $listenerEvents;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @throws APIException
     */
    protected function initializePayload()
    {
        APILogger::addDebug('Decoding buffer using file_get_contents()');

        $payload = json_decode(
            file_get_contents('php://stdin'),
            true
        );

        if (!isset($payload['Records'])) {
            throw new APIException(
                'Error decoding buffer',
                ['json_error' => json_last_error(), 'buffer' => $payload]
            );
        }

        $this->setPayload($payload);
    }

    /**
     * @param string $schemaName
     *
     * @throws APIException
     */
    protected function initializeListenerEvents($schemaName = '')
    {
        APILogger::addDebug('Adding ' . count($this->getPayload()['Records']) . ' record(s)');

        foreach ($this->getPayload()['Records'] as $record) {
            $this->getListenerEvents()->addEvent(
                $record,
                $schemaName
            );
        }
    }

    /**
     * @param ListenerEvents $listenerEvents
     * @param string $schemaName
     */
    public function process(ListenerEvents $listenerEvents, $schemaName = '')
    {
        try {
            $this->setListenerEvents($listenerEvents);

            $this->initializeListenerEvents($schemaName);

            $listenerResult = $this->processListenerEvents();

            if (!$listenerResult instanceof ListenerResult) {
                throw new APIException(
                    'Listener did not return a ListenerResult object',
                    ['ListenerResult' => $listenerResult]
                );
            }

            echo json_encode($listenerResult);
        } catch (\Throwable $exception) {
            APILogger::addError($exception->getMessage(), $exception);

            echo json_encode(
                new ListenerResult(
                    false,
                    $exception->getMessage()
                )
            );
        }
    }
}
