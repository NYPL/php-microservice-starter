<?php
namespace NYPL\Starter;

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Service extends App
{
    public function __construct(Container $container = null)
    {
        ini_set('display_errors', 0);
        set_error_handler(ErrorHandler::class . "::errorFunction");
        register_shutdown_function(ErrorHandler::class . "::shutdownFunction");

        if (!$container) {
            $container = new DefaultContainer();
        }

        parent::__construct($container);

        $this->setupDefaultRoutes();
    }

    protected function setupDefaultRoutes()
    {
        $this->add(function (Request $request, Response $response, callable $next) {
            $response = $response
                ->withHeader(
                    "Access-Control-Allow-Headers",
                    "Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token"
                )
                ->withHeader(
                    "Access-Control-Allow-Methods",
                    "GET, POST, PUT, DELETE, OPTIONS"
                )
                ->withHeader(
                    "Access-Control-Allow-Origin",
                    "*"
                );

            return $next($request, $response);
        });

        $this->options("[/{params:.*}]", function (Request $request, Response $response) {
            return $response;
        });
    }
}
