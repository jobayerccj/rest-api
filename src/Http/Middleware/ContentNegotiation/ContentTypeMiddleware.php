<?php

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ContentTypeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ContentTypeNegotiator $negotiator,
    ) {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $request = $this->negotiator->negotiate($request);
        $response = $handler->handle($request);
        return $response->withHeader('Content-Type', $request->getAttribute('content-type')->value);
    }
}
