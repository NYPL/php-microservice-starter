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
