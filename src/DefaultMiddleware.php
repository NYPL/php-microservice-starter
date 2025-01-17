<?php

namespace NYPL\Starter;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class DefaultMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {

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
    }
}
