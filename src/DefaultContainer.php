<?php
namespace NYPL\Starter;

use Slim\Http\Request;
use Slim\Http\Response;
use NYPL\Starter\Model\Response\ErrorResponse;
use Slim\Container;
use Slim\HttpCache\CacheProvider;

class DefaultContainer extends Container
{
    public function __construct()
    {
        parent::__construct();

        $this["settings"]["displayErrorDetails"] = false;

        $this["cache"] = function () {
            return new CacheProvider();
        };

        $this["notFoundHandler"] = function (Container $container) {
            return function (Request $request, Response $response) use ($container) {
                return $container["response"]
                    ->withStatus(404)
                    ->withHeader("Content-Type", "text/html")
                    ->write("Page not found");
            };
        };

        $this["errorHandler"] = function (Container $container) {
            return function (Request $request, Response $response, \Exception $exception) use ($container) {
                if ($exception instanceof APIException) {
                    $errorCode = $exception->getHttpCode();
                } else {
                    $errorCode = 500;
                }

                APILogger::addLog(
                    $errorCode,
                    $exception->getMessage(),
                    [
                        (string) $request->getUri(),
                        $request->getParsedBody()
                    ]
                );

                return $container["response"]
                    ->withStatus($errorCode)
                    ->withJson(new ErrorResponse(
                        $errorCode,
                        "exception",
                        $exception->getMessage(),
                        $exception
                    ))
                    ->withHeader(
                        "Access-Control-Allow-Origin",
                        "*"
                    );
            };
        };

        $this["phpErrorHandler"] = function (Container $container) {
            return function (Request $request, Response $response, \Throwable $error) use ($container) {
                $defaultErrorHttpCode = 500;

                APILogger::addError(
                    $error->getMessage(),
                    [
                        (string) $request->getUri(),
                        $request->getParsedBody()
                    ]
                );

                return $container["response"]
                    ->withStatus($defaultErrorHttpCode)
                    ->withJson(new ErrorResponse(
                        $defaultErrorHttpCode,
                        "error",
                        $error->getMessage(),
                        $error
                    ))
                    ->withHeader(
                        "Access-Control-Allow-Origin",
                        "*"
                    );
            };
        };
    }
}
