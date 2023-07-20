<?php

namespace Paulzer\PhpForestFireSimulation\Service;

class SimulationParameters
{
    const PARAMETERS_FILE_PATH = __DIR__ . '/../../config/simulation_parameters.json';

    private int $forestGridWidth;
    private int $forestGridHeight;
    private array $initialFireCoordinates;
    private float $propagationProbability;

    public function __construct()
    {
        $this->buildFromFile();
    }

    public function buildFromFile(): void
    {
        $paramsArray = json_decode(file_get_contents(self::PARAMETERS_FILE_PATH), true);

        $this->forestGridWidth = (int) $paramsArray['forestGridWidth'];
        $this->forestGridHeight = (int) $paramsArray['forestGridHeight'];

        $this->initialFireCoordinates = $paramsArray['initialFireCoordinates'];

        $this->propagationProbability = (float) $paramsArray['propagationProbability'];


    }

    public function getForestGridWidth(): int
    {
        return $this->forestGridWidth;
    }

    public function getForestGridHeight(): int
    {
        return $this->forestGridHeight;
    }

    public function getInitialFireCoordinates(): array
    {
        return $this->initialFireCoordinates;
    }

    public function getPropagationProbability(): float
    {
        return $this->propagationProbability;
    }
}