<?php

namespace Paulzer\PhpForestFireSimulation;

use Laminas\Diactoros\Response;
use Paulzer\PhpForestFireSimulation\Entity\ForestGrid;
use Paulzer\PhpForestFireSimulation\Service\SimulationParameters;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ForestFireSimulation implements MessageComponentInterface
{
    private ?string $initiatorConnectionId = null;
    private string $status;
    private Environment $twig;
    private SimulationParameters $simulationParameters;
    private ForestGrid $forestGrid;
    private int $currentTime = 0;



    public function __construct(SimulationParameters $simulationParameters)
    {
        $this->twig = new Environment(
            new FilesystemLoader(dirname(__DIR__) . '/src/Templates')
        );

        $this->simulationParameters = $simulationParameters;
        $this->initialize();

        echo "\nSimulation server is up and listening for WebSocket connections from your browser !\n\n";
    }

    function onOpen(ConnectionInterface $conn)
    {
        echo "New connection ! \n";
        $this->sendViewResponse($conn);
    }

    function onClose(ConnectionInterface $conn)
    {
        echo "Connection closed ! \n";
    }
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        $msgData = json_decode($msg->getPayload(), true);

        if(null === $this->initiatorConnectionId){
            $this->initiatorConnectionId = $msgData['connId'];
            echo "Initiator connection id is {$this->initiatorConnectionId} \n";
        }

        if(isset($msgData['action'])){
            switch ($msgData['action']){
                case 'iterate':
                    if($this->initiatorConnectionId === $msgData['connId']){
                        $this->iterate();

                        if(false === $this->forestGrid->hasOnFireCells()){
                            $this->status = 'over';
                        }
                        break;
                    }

                    case 'reset':
                        $this->initiatorConnectionId = $msgData['connId'];
                        echo "Initiator connection id is {$this->initiatorConnectionId} \n";
                        $this->initialize();
                        break;
            }
        }

        $this->sendViewResponse($conn);
    }

    private function initialize(): void
    {
        $this->currentTime = 0;
        $this->forestGrid = new ForestGrid($this->simulationParameters);

        $this->status = 'initialized';
        echo "Simulation initialized ! \n";
    }

    private function iterate(): void
    {
        if(false === $this->forestGrid->hasOnFireCells()){
            return;
        }

        $this->status = 'running';

        $this->forestGrid->iterate($this->simulationParameters->getPropagationProbability());

        $this->currentTime++;
    }

    private function sendViewResponse(ConnectionInterface $conn): void
    {
        $view = $this->twig->render('forestGrid.html.twig', [
            'simulation' => $this,
        ]);

        $conn->send(json_encode([
            'status' => $this->status,
            'html' => $view
        ]));
    }

    public function getSimulationParameters(): SimulationParameters
    {
        return $this->simulationParameters;
    }

    public function getForestGrid(): ForestGrid
    {
        return $this->forestGrid;
    }

    /**
     * @return int
     */
    public function getCurrentTime(): int
    {
        return $this->currentTime;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}