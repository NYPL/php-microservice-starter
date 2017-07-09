<?php
namespace NYPL\Starter\Listener;

use NYPL\Starter\Listener\ListenerEvent\KinesisEvent;
use NYPL\Starter\Listener\ListenerEvents\KinesisEvents;
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
     * Listener constructor.
     */
    public function __construct()
    {
        set_error_handler(self::class . "::errorHandler");

        register_shutdown_function(self::class . '::fatalHandler');
    }

    public static function fatalHandler()
    {
        $error = error_get_last();

        if ($error !== null) {
            error_log(
                json_encode([
                    'message' => $error['message'],
                    'level' => 500,
                    'level_name' => 'ERROR'
                ])
            );
        }
    }

    /**
     * @param int $errorNumber
     * @param string $errorString
     * @param string $errorFile
     * @param string $errorLine
     * @param array $errorContext
     */
    public static function errorHandler($errorNumber = 0, $errorString = '', $errorFile = '', $errorLine = '', array $errorContext)
    {
        APILogger::addError(
            'Error ' . $errorNumber . ': ' . $errorString . ' in ' . $errorFile . ' on line ' . $errorLine,
            $errorContext
        );
    }

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
     * @param array $record
     *
     * @return bool
     * @throws APIException
     */
    protected function addEvent(array $record)
    {
        if ($this->getListenerEvents() instanceof KinesisEvents) {
            $this->addKinesisEvent($record);
            return true;
        }

        throw new APIException(
            'Listener event type was not found'
        );
    }

    /**
     * @param array $record
     */
    protected function addKinesisEvent(array $record)
    {
        $this->getListenerEvents()->addEvent(
            new KinesisEvent(
                new ListenerData(
                    base64_decode($record['kinesis']['data']),
                    $this->getSchemaName()
                ),
                $this->getStreamNameFromArn($record['eventSourceARN'])
            )
        );
    }

    /**
     * @throws APIException
     */
    protected function initializeListenerEvents()
    {
        APILogger::addInfo('Decoding buffer using file_get_contents()');

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

        APILogger::addInfo('Decoding ' . count($buffer['Records']) . ' records');

        foreach ($buffer['Records'] as $record) {
            try {
                $this->addEvent($record);
            } catch (\Exception $exception) {
                APILogger::addError(
                    $exception->getMessage(),
                    $exception
                );
            }
        }
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

        APILogger::addInfo(
            'Processing record in ' . $streamName . ' stream.'
        );

        return $streamName;
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
