<?php

namespace Paulzer\PhpForestFireSimulation\Controller;

use Paulzer\PhpForestFireSimulation\Entity\ForestGrid;
use Paulzer\PhpForestFireSimulation\Service\SimulationParameters;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ForestFireSimulationController
{
    private Environment $twig;
    private ResponseInterface $response;

    public function __construct(Environment $twig, ResponseInterface $response)
    {
        $this->twig = $twig;
        $this->response = $response;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');

        $html = $this->twig->render('base.html.twig');

        $response->getBody()
            ->write($html);

        return $response;
    }
}