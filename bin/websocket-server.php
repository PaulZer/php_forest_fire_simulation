<?php

use Paulzer\PhpForestFireSimulation\ForestFireSimulation;
use Paulzer\PhpForestFireSimulation\Service\SimulationParameters;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ForestFireSimulation(new SimulationParameters())
        )
    ),
    8088
);

$server->run();