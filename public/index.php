<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Laminas\Diactoros\Response;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use Relay\Relay;
use Laminas\Diactoros\ServerRequestFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function DI\create;
use function DI\get;
use function FastRoute\simpleDispatcher;

use Paulzer\PhpForestFireSimulation\Controller\ForestFireSimulationController;


require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAttributes(false);

$containerBuilder->addDefinitions([
    ForestFireSimulationController::class => create(ForestFireSimulationController::class)
        ->constructor(
            get('Twig'),
            get('Response')),
    'Twig' => function() {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/src/Templates');
        return new Environment($loader, [
            'cache' => false,
        ]);
    },
    'Response' => function() {
        return new Response();
    }
]);


try {
    $container = $containerBuilder->build();

    $routes = simpleDispatcher(function (RouteCollector $r) {
        $r->get('/forest-fire-simulation', ForestFireSimulationController::class);
    });

    $middlewareQueue = [];
    $middlewareQueue[] = new FastRoute($routes);
    $middlewareQueue[] = new RequestHandler($container);

    $requestHandler = new Relay($middlewareQueue);

    $response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

    $emitter = new SapiEmitter();
    return $emitter->emit($response);

} catch (Exception $e) {
    var_dump($e);
    exit;
}