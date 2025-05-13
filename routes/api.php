<?php

declare(strict_types=1);

use App\Controller\FlightsController;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Changing the default invocation strategy on the RouteCollector component
 * will change it for every route being defined after this change being applied
 */
$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy(new RequestResponseArgs());

// Define routes
$app->get('/healthcheck', function (Request $request, Response $response) {
    $payload = json_encode(['app' => true]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/flights', [FlightsController::class, 'index']);

$app->get(
    '/flights/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
    [FlightsController::class,'show']
);

$app->post('/flights', [FlightsController::class, 'store']);

$app->delete(
    '/flights/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
    [FlightsController::class,'destroy']
);

$app->put(
    '/flights/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
    [FlightsController::class,'update']
);

$app->patch(
    '/flights/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
    [FlightsController::class,'update']
);