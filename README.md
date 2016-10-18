# NYPL PHP Microservice Starter

This package is intended to be used as the starter package for PHP-based NYPL Microservices.

This package adheres to [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/) (using the [Composer](https://getcomposer.org/) autoloader).

## Installation

Via Composer
~~~~
"require": {
    "nypl/microservice-starter": "~0.1"
}
~~~~

## Requirements

* PHP >=5.5.0
* PHP Extensions
    + [Rdkafka](https://arnaud-lb.github.io/php-rdkafka/phpdoc/book.rdkafka.html)

## Features

* RESTful HTTP framework ([Slim](http://www.slimframework.com/))
* Database PDO library ([Slim-PDO](https://github.com/FaaPz/Slim-PDO))
* Kafka message publishing ([Rdkafka](https://arnaud-lb.github.io/php-rdkafka/phpdoc/book.rdkafka.html))
* Avro serializer ([Avro](http://apache.osuosl.org/avro/))
* Swagger documentation generator ([swagger-php](https://github.com/zircote/swagger-php))
* Error logging ([Monolog](https://github.com/Seldaek/monolog))
* Identity/JWT authentication via NYPL API Gateway (`X-NYPL-Identity`)

## Usage

### HTTP/API Server

See the `samples/service` directory to learn how to create an example service.

#### Basic Example

Create an `index.php` with a `Service` object and your [Slim](http://www.slimframework.com/) routes:

~~~~
$service = new NYPL\Starter\Service();

$service->get("/v0.1/bibs", function (Request $request, Response $response) {
    $controller = new Controller\BibController($request, $response);
    return $controller->getBibs();
});
~~~~

Configure your web server to load `index.php` on all requests.
See the `samples/service-config` directory for sample configuration files for an Apache `.htaccess` or Nginx `nginx.conf` installation.

### Swagger Documentation Generator

Create a Swagger route to generate Swagger specification documentation:

~~~~
$service->get("/swagger", function (Request $request, Response $response) {
    return SwaggerGenerator::generate(__DIR__ . "/src", $response);
});
~~~~
