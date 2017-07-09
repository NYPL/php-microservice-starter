<?php
namespace NYPL\Starter\Listener;

use NYPL\Starter\APIException;
use NYPL\Starter\APILogger;

abstract class Listener
{
    /**
     * @var ListenerEvents
     */
    protected $listenerEvents;

    /**
     * @var string
     */
    protected $schemaName = '';

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
     * @return string
     */
    protected function getSchemaName()
    {
        return $this->schemaName;
    }

    /**
     * @param string $schemaName
     */
    protected function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
    }

    /**
     * @throws APIException
     */
    protected function initializeListenerEvents()
    {
        APILogger::addDebug('Decoding buffer using file_get_contents()');

        $buffer = json_decode(
            file_get_contents('php://stdin'),
            true
        );

        if (!isset($buffer['Records'])) {
            throw new APIException(
                'Error decoding buffer',
                ['json_error' => json_last_error(), 'buffer' => $buffer]
            );
        }

        APILogger::addDebug('Decoding ' . count($buffer['Records']) . ' records');

        foreach ($buffer['Records'] as $record) {
            $this->getListenerEvents()->addEvent(
                $record,
                $this->getSchemaName()
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
            $this->setSchemaName($schemaName);
            $this->setListenerEvents($listenerEvents);

            $this->initializeListenerEvents();

            $listenerResult = $this->processListenerEvents();

            if (!$listenerResult instanceof ListenerResult) {
                throw new APIException(
                    'Listener did not return a proper result'
                );
            }

            echo json_encode($listenerResult);
        } catch (\Throwable $exception) {
            echo json_encode(
                new ListenerResult(
                    false,
                    $exception->getMessage()
                )
            );
        } catch (\Exception $exception) {
            echo json_encode(
                new ListenerResult(
                    false,
                    $exception->getMessage()
                )
            );
        }
    }
}
