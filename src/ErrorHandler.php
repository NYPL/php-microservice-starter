<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\Response\ErrorResponse;

class ErrorHandler
{
    /**
     * @var bool
     */
    protected static $ignoreError = false;

    /**
     * @param string $errorString
     * @param array|object $context
     */
    public static function processShutdownError($errorString = '', $context = [])
    {
        if (!self::isIgnoreError()) {
            $exception = new APIException($errorString, $context);

            APILogger::addError($errorString, $exception);

            $apiResponse = new ErrorResponse(
                500,
                'error',
                $errorString,
                $exception
            );

            ob_clean();
            http_response_code(500);
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($apiResponse, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }
    }

    /**
     * @return bool
     */
    public static function isIgnoreError()
    {
        return self::$ignoreError;
    }

    /**
     * @param bool $ignoreError
     */
    public static function setIgnoreError($ignoreError)
    {
        self::$ignoreError = (bool) $ignoreError;
    }


    public static function errorFunction($errorNumber, $errorString, $errorFile, $errorLine, array $errorContext = [])
    {
        if (!self::isIgnoreError()) {
            APILogger::addError(
                $errorString,
                [
                    'file' => $errorFile,
                    'line' => $errorLine,
                ]
            );
        }
    }

    public static function shutdownFunction()
    {
        $error = error_get_last();

        if ($error !== null) {
            self::processShutdownError($error['message'], $error);
        }
    }
}
