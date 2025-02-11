<?php
namespace NYPL\Starter;

use Aura\Di\Injection\InjectionFactory;
use GuzzleHttp\Psr7\Stream;
use NYPL\Starter\Model\Response\ErrorResponse;
use Aura\Di\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DefaultContainer extends Container
{
    const DEFAULT_ERROR_STATUS_CODE = 500;

    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @param \Throwable $exception
     *
     * @return int
     */
    protected function getStatusCode(\Throwable $exception): int {
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
    protected function initializeErrorResponse(\Throwable $exception, ErrorResponse $errorResponse) {
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
    protected function getErrorResponse(\Throwable $exception): ErrorResponse {
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
    protected function logError(Request $request, $exception) {
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

    protected function handleError(Container $container, Request $request, \Throwable $exception) {
        $this->logError($request, $exception);

        $json = json_encode($this->getErrorResponse($exception));
        $streamBody = fopen('data://text/plain,' . $json, 'r');
        return $container["response"]
            ->withStatus($this->getStatusCode($exception))
            ->withBody(new Stream($streamBody))
            ->withHeader("Access-Control-Allow-Origin", "*");
    }

    public function __construct(
        InjectionFactory $injectionFactory,
        ContainerInterface $delegateContainer = null
    ) {
        parent::__construct($injectionFactory, $delegateContainer);

        $this->settings["displayErrorDetails"] = false;
    }

    public function notFoundHandler(Container $container) {
        return function (Request $request, Response $response) use ($container) {
            return $container["response"]
                ->withStatus(404)
                ->withHeader("Content-Type", "text/html")
                ->write("Page not found");
        };
    }

    public function phpErrorHandler(Container $container) {
        return function (Request $request, Response $response, \Throwable $exception) use ($container) {
            return $this->handleError($container, $request, $exception);
        };
    }

    public function errorHandler(Container $container) {
        return function (Request $request, Response $response, \Throwable $exception) use ($container) {
            return $this->handleError($container, $request, $exception);
        };
    }

    /**
     * Initialize Services.
     * Use this to add Request and Response to container before executing Controller callback.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws \Aura\Di\Exception\ContainerLocked
     * @throws \Aura\Di\Exception\ServiceNotObject
     */
    public function initServices(Request $request, Response $response) {
        $this->set('request', $request);
        $this->set('response', $response);
    }

}
