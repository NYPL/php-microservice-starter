<?php
namespace NYPL\Starter;

use Aura\Di\Container;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Service extends App
{
    const CACHE_SECONDS_OPTIONS_REQUEST = 600;

    public function __construct(Container $container = null)
    {
        $_SERVER['SCRIPT_NAME'] = 'index.php';

        set_error_handler(ErrorHandler::class . "::errorFunction");
        register_shutdown_function(ErrorHandler::class . "::shutdownFunction");

        if (!$container) {
            $container = new DefaultContainer();
        }

        AppFactory::setContainer($container);
        $app = AppFactory::create();

        parent::__construct($app->getResponseFactory(), $container);

        $this->setupDefaultRoutes();
    }

    protected function setupDefaultRoutes()
    {
        $this->add(function (Request $request, Response $response, callable $next) {
            $response = $next($request, $response);
            return $response
                ->withHeader(
                    "Access-Control-Allow-Headers",
                    "Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token"
                )
                ->withHeader(
                    "Access-Control-Allow-Methods",
                    "GET, POST, PUT, PATCH, DELETE, OPTIONS"
                )
                ->withHeader(
                    "Access-Control-Allow-Origin",
                    "*"
                )
                ->withHeader(
                    "Access-Control-Allow-Credentials",
                    "true"
                )
                ->withHeader('X-NYPL-Original-Request', $request->getUri())
                ->withHeader('X-NYPL-Response-Date', date('c'));
        });

        $this->options("[/{params:.*}]", function (Request $request, Response $response) {
            return $response
                ->withHeader(
                    "Cache-Control",
                    "public, max-age=" . self::CACHE_SECONDS_OPTIONS_REQUEST
                );
        });
    }
}
