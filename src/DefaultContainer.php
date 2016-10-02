<?php
namespace NYPL\API;

use Slim\Http\Request;
use Slim\Http\Response;
use NYPL\API\Model\Response\ErrorResponse;
use Slim\Container;

class DefaultContainer extends Container
{
    public function __construct()
    {
        parent::__construct();

        $this["settings"]["displayErrorDetails"] = true;

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
                return $container["response"]->withStatus(500)
                    ->withJson(new ErrorResponse(
                        500,
                        'exception',
                        $exception->getMessage(),
                        $exception
                    ));
            };
        };
    }
}
