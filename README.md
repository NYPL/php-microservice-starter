# NYPL PHP Microservice Starter

This package is intended to be used as the starter package for PHP-based NYPL Microservices.

This package adheres to [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/) (using the [Composer](https://getcomposer.org/) autoloader).

## Deployment

This library is intended to be used as a Composer dependency hosted on Packagist. To deploy a new version, create a 
numeric tag, like "2.0.0" and push it up to [Github](https://github.com/NYPL/php-microservice-starter). Then, log into 
[Packagist](https://packagist.org/packages/nypl/microservice-starter) and hit the update button to pull in the new tag.
This will make your new tag available as a Composer dependency version that your apps can access.

## Installation

Via Composer
~~~~
"require": {
    "nypl/microservice-starter": "^2.0.0"
}
~~~~

## Requirements

* PHP >= 8.3
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

### Forked Avro library

A fork of the Avro PHP library is included in this repo. See [the Avro README](lib/Avro/README.md) for details.

