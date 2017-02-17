<?php
namespace NYPL\Starter;

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Service extends App
{
    const CACHE_SECONDS_OPTIONS_REQUEST = 600;

    public function __construct(Container $container = null)
    {
        set_error_handler(ErrorHandler::class . "::errorFunction");
        register_shutdown_function(ErrorHandler::class . "::shutdownFunction");

        date_default_timezone_set(Config::get('TIME_ZONE'));

        if (!$container) {
            $container = new DefaultContainer();
        }

        if (extension_loaded('xhprof') && Config::get('XH_PROF_DIRECTORY')) {
            include_once Config::get('XH_PROF_DIRECTORY') . '/xhprof_lib/utils/xhprof_lib.php';
            include_once Config::get('XH_PROF_DIRECTORY') . '/xhprof_lib/utils/xhprof_runs.php';
            xhprof_enable();
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
                    "GET, POST, PUT, PATCH, DELETE, OPTIONS"
                )
                ->withHeader(
                    "Access-Control-Allow-Origin",
                    "*"
                )
                ->withHeader(
                    "Access-Control-Allow-Credentials",
                    "true"
                );

            $response = $response
                ->withHeader('X-NYPL-Original-Request', $request->getUri())
                ->withHeader('X-NYPL-Response-Date', date('c'));

            return $next($request, $response);
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
