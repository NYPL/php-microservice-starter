<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\Response\ErrorResponse;

class ErrorHandler
{
    /**
     * @var bool
     */
    protected static $ignoreError = false;

    public static function processError($errorString = '', array $context = [])
    {
        if (!self::isIgnoreError()) {
            $exception = new APIException($errorString, $context);

            $apiResponse = new ErrorResponse(
                500,
                'error',
                'There was an error processing your request.',
                $exception
            );

            APILogger::addError($errorString, (array) $exception);

            http_response_code(500);
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($apiResponse, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            die();
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


    public static function errorFunction($errorNumber, $errorString, $errorFile, $errorLine, array $errorContext)
    {
        self::processError(
            $errorString . ' (' . $errorNumber . ') in ' . $errorFile . ' on line ' . $errorLine,
            $errorContext
        );
    }

    public static function shutdownFunction()
    {
        $error = error_get_last();

        if ($error !== null) {
            self::processError($error['message'], $error);
        }
    }
}
