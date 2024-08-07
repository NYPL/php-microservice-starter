<?php
namespace NYPL\Starter;

use NYPL\Starter\Model\Response\ErrorResponse;
use Aura\DI\Container;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DefaultContainer extends Container
{
    const DEFAULT_ERROR_STATUS_CODE = 500;

    /**
     * @param \Throwable $exception
     *
     * @return int
     */
    protected function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof APIException) {
            return $exception->getHttpCode();
        }

        return self::DEFAULT_ERROR_STATUS_CODE;
    }

    /**
     * @param \Throwable $exception
     * @param ErrorResponse $errorResponse
     *
     * @throws APIException
     */
    protected function initializeErrorResponse(\Throwable $exception, ErrorResponse $errorResponse)
    {
        $errorResponse->setStatusCode($this->getStatusCode($exception));
        $errorResponse->setType('exception');
        $errorResponse->setMessage($exception->getMessage());

        if (!Config::isProductionEnvironment()) {
            $errorResponse->setError($errorResponse->translateException($exception));
        }
    }

    /**
     * @param \Throwable $exception
     *
     * @return ErrorResponse
     * @throws APIException
     */
    protected function getErrorResponse(\Throwable $exception): ErrorResponse
    {
        if ($exception instanceof APIException && $exception->getErrorResponse()) {
            $errorResponse = $exception->getErrorResponse();
        } else {
            $errorResponse = new ErrorResponse();
        }

        $this->initializeErrorResponse($exception, $errorResponse);

        return $errorResponse;
    }

    /**
     * @param Request $request
     * @param \Exception|\Throwable $exception
     */
    protected function logError(Request $request, $exception)
    {
        APILogger::addLog(
            $this->getStatusCode($exception),
            $exception->getMessage(),
            [
                $request->getHeaderLine('X-NYPL-Log-Stream-Name'),
                $request->getHeaderLine('X-NYPL-Request-ID'),
                (string) $request->getUri(),
                $request->getParsedBody()
            ]
        );
    }

    protected function handleError(Container $container, Request $request, \Throwable $exception)
    {
        $this->logError($request, $exception);

        return $container["response"]
            ->withStatus($this->getStatusCode($exception))
            ->withJson($this->getErrorResponse($exception))
            ->withHeader("Access-Control-Allow-Origin", "*");
    }

    public function __construct()
    {
        parent::__construct();

        $this["settings"]["displayErrorDetails"] = false;

        $this["notFoundHandler"] = function (Container $container) {
            return function (Request $request, Response $response) use ($container) {
                return $container["response"]
                    ->withStatus(404)
                    ->withHeader("Content-Type", "text/html")
                    ->write("Page not found");
            };
        };

        $this['phpErrorHandler'] = function ($container) {
            return function (Request $request, Response $response, \Throwable $exception) use ($container) {
                return $this->handleError($container, $request, $exception);
            };
        };

        $this["errorHandler"] = function (Container $container) {
            return function (Request $request, Response $response, \Throwable $exception) use ($container) {
                return $this->handleError($container, $request, $exception);
            };
        };
    }
}
