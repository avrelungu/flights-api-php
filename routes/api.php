<?php

declare(strict_types=1);

use App\Controller\FlightsController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\Strategies\RequestResponseArgs;

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

$app->group('/flights', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [\App\Controller\FlightsController::class, 'index']);

    $group->get(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [\App\Controller\FlightsController::class, 'show']
    );

    $group->post('', [\App\Controller\FlightsController::class, 'store']);

    $group->delete(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [\App\Controller\FlightsController::class, 'destroy']
    );

    $group->put(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [\App\Controller\FlightsController::class, 'update']
    );

    $group->patch(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [\App\Controller\FlightsController::class, 'update']
    );
});

$app->group('/passengers', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [\App\Controller\PassengersController::class, 'index']);

    $group->get(
        '/{reference:[0-9]+[A-Z]{3}}',
        [\App\Controller\PassengersController::class, 'show']
    );

    $group->post('', [\App\Controller\PassengersController::class, 'store']);

    $group->delete(
        '/{reference:[0-9]+[A-Z]{3}}',
        [\App\Controller\PassengersController::class, 'destroy']
    );

    $group->put(
        '/{reference:[0-9]+[A-Z]{3}}',
        [\App\Controller\PassengersController::class, 'update']
    );

    $group->patch(
        '/{reference:[0-9]+[A-Z]{3}}',
        [\App\Controller\PassengersController::class, 'update']
    );
});