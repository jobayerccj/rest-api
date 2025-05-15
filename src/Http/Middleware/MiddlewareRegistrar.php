<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Slim\App;
use Middlewares\TrailingSlash;
use App\Http\Error\HttpErrorHandler;
use App\Http\Middleware\ContentNegotiation\ContentTypeNegotiator;

/**
 * Registers middleware
 *
 * Order is important.
 */
final readonly class MiddlewareRegistrar
{
    public function __construct(
        private App $app
    ) {
    }

    public function register(): void
    {
        $this->registerCustomMiddleware();
        $this->registerDefaultMiddleware();
        $this->addErrorMiddleware();
    }

    private function registerCustomMiddleware(): void
    {
        $app = $this->app;

        $app->add(new ContentNegotiation\ContentTypeMiddleware(new ContentTypeNegotiator()));
    }

    private function registerDefaultMiddleware(): void
    {
        $app = $this->app;

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $app->add(new TrailingSlash(false));
    }

    private function addErrorMiddleware(): void
    {
        $errorMiddleware = $this->app->addErrorMiddleware(true, true, true);
        $callableResolver = $this->app->getCallableResolver();
        $errorMiddleware->setDefaultErrorHandler(
            new HttpErrorHandler($callableResolver, $this->app->getResponseFactory())
        );
    }
}
