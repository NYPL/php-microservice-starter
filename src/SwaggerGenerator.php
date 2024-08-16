<?php
namespace NYPL\Starter;

use GuzzleHttp\Psr7\Response;
use OpenApi\Generator;
use OpenApi\Loggers\DefaultLogger;

class SwaggerGenerator
{
    public static function generate(array $directory, Response $response)
    {
        //ErrorHandler::setIgnoreError(true);

        $generator = new Generator(new DefaultLogger());

        $openapi = $generator->scan($directory, ['exclude' => ['tests'], 'pattern' => '*.php']);

//        $openapi->host = Config::get('SWAGGER_HOST');
//
//        $openapi->basePath = '/api';
//
//        $openapi->schemes = [
//            Config::get('SWAGGER_SCHEME')
//        ];

        $json = $openapi->toJson();


        $streamBody = fopen('data://text/plain,' . $json,'r');

        return $response->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token'
                )
                ->withBody(new \GuzzleHttp\Psr7\Stream($streamBody));


//
//        $finder = \Symfony\Component\Finder\Finder::create()->files()->name('*.php')->in($directory);
//
//        $openapi = (new \OpenApi\Generator())->generate([$directory, $finder]);
//
//        $json = $openapi->toJson();
//
//
//        $streamBody = fopen('data://text/plain,' . $json,'r');
//
//        return $response->withHeader('Content-Type', 'application/json')
//                ->withHeader('Access-Control-Allow-Origin', '*')
//                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
//                ->withHeader('Access-Control-Allow-Credentials', 'true')
//                ->withHeader(
//                    'Access-Control-Allow-Headers',
//                    'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token'
//                )
//                ->withBody(new \GuzzleHttp\Psr7\Stream($streamBody));

    }
}
