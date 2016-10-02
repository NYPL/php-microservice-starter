<?php
require "vendor/autoload.php";
require "avro-php-1.8.1/lib/avro.php";

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use NYPL\API\Controller;
use NYPL\API\DefaultContainer;

$app = new App(new DefaultContainer());

$app->add(function (Request $request, Response $response, callable $next) {
    $response = $response->withHeader(
        "Access-Control-Allow-Headers",
        "Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token"
    );
    $response = $response->withHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    $response = $response->withHeader("Access-Control-Allow-Origin", "*");

    $response = $next($request, $response);

    return $response;
});

$app->options("[/{params:.*}]", function (Request $request, Response $response) {
    return $response;
});

$app->post("/v0.1/bibs", function (Request $request, Response $response) {
    $controller = new Controller\BibController($request, $response);
    return $controller->createBib();
});

$app->get("/v0.1/bibs", function (Request $request, Response $response) {
    $controller = new Controller\BibController($request, $response);
    return $controller->getBibs();
});

$app->get("/v0.1/bibs/{id}", function (Request $request, Response $response, $parameters) {
    $controller = new Controller\BibController($request, $response);
    return $controller->getBib($parameters["id"]);
});

$app->run();
