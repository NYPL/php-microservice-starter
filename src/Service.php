<?php
namespace NYPL\Starter;

use Aura\Di\Container;
use Aura\Di\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;

class Service extends App
{
    const CACHE_SECONDS_OPTIONS_REQUEST = 600;

    public function __construct(Container $container = null)
    {
        $_SERVER['SCRIPT_NAME'] = 'index.php';

        set_error_handler(ErrorHandler::class . "::errorFunction");
        register_shutdown_function(ErrorHandler::class . "::shutdownFunction");

        if (!$container) {
            $builder = new ContainerBuilder();
            $container = $builder->newInstance();
        }

        AppFactory::setContainer($container);
        $app = AppFactory::create();

        $app->addBodyParsingMiddleware();

        parent::__construct($app->getResponseFactory(), $container);
    }
}
