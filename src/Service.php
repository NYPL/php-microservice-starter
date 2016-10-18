<?php
namespace NYPL\API;

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Service extends App
{
    public function __construct(Container $container = null)
    {
        if (!$container) {
            $container = new DefaultContainer();
        }

        parent::__construct($container);

        $this->setupDefaultRoutes();
    }

    protected function setupDefaultRoutes()
    {
        $this->add(function (Request $request, Response $response, callable $next) {
            $response = $response->withHeader(
                "Access-Control-Allow-Headers",
                "Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token"
            );
            $response = $response->withHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
            $response = $response->withHeader("Access-Control-Allow-Origin", "*");

            $response = $next($request, $response);

            return $response;
        });

        $this->options("[/{params:.*}]", function (Request $request, Response $response) {
            return $response;
        });
    }
}
